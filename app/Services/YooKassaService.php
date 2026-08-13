<?php

namespace App\Services;

use App\Actions\TelegramSendAction;
use App\Mail\Cart\PaymentStatusSend;
use App\Models\Order;
use App\Models\ShopOrder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use YooKassa\Client;
use YooKassa\Model\Notification\NotificationEventType;
use YooKassa\Model\Notification\NotificationFactory;

class YooKassaService
{
    public function pay_fixation()
    {
        try {
            Log::channel('pay')->info('Пришел запрос на фиксацию оплаты');
            $source = file_get_contents('php://input');
            $data = json_decode($source, true);

            $factory = new NotificationFactory;
            $notificationObject = $factory->factory($data);
            $responseObject = $notificationObject->getObject();

            $client = new \YooKassa\Client;

            if (! config('yookassa.shop_id')) {
                if (! $client->isNotificationIPTrusted($_SERVER['REMOTE_ADDR'])) {
                    header('HTTP/1.1 400 Something went wrong (IP)');
                    exit();
                }
            }

            if ($notificationObject->getEvent() === NotificationEventType::PAYMENT_SUCCEEDED) {
                $someData = [
                    'paymentId' => $responseObject->getId(),
                    'paymentStatus' => $responseObject->getStatus(),
                ];
                Log::channel('pay')->info('Payment succeeded: '.$responseObject->getId());

            } elseif ($notificationObject->getEvent() === NotificationEventType::PAYMENT_CANCELED) {
                $someData = [
                    'paymentId' => $responseObject->getId(),
                    'paymentStatus' => $responseObject->getStatus(),
                ];
                Log::channel('pay')->info('Payment concled: '.$responseObject->getId());
            } else {
                header('HTTP/1.1 400 Something went wrong (Type)');
                exit();
            }

            $client->setAuth(config('yookassa.shop_id'), config('yookassa.secret_key'));

            if ($paymentInfo = $client->getPaymentInfo($someData['paymentId'])) {
                $paymentStatus = $paymentInfo->getStatus();

                $order = ShopOrder::where('payment_id', $someData['paymentId'])->first();

                if (! $order) {
                    $order = Order::where('pay_order', $someData['paymentId'])->first();
                }

                if ($order) {
                    if ($order instanceof ShopOrder) {
                        $order->payment_status = $someData['paymentStatus'];
                        $order->payment_status_text = $someData['paymentStatus'] === 'succeeded' ? 'Оплачен' : 'Не оплачен';
                        $order->save();
                    } else {
                        $order->pay_status = 1;
                        $order->pay_status_text = $someData['paymentStatus'];
                        $order->save();
                    }

                    $orderStatusText = ($someData['paymentStatus'] === 'succeeded') ? 'Оплачен' : 'Не оплачен';

                    $amount = number_format((float) ($paymentInfo['amount']['value'] ?? 0), 2, '.', '');

                    $pay_text = '<b>Заказ №'.$order->id.' '.$orderStatusText." </b>\n\r";
                    $pay_text .= '<b>ID: </b>'.$someData['paymentId']."\n\r";
                    $pay_text .= '<b>Сумма: </b>'.$amount." ₽\n\r";

                    if ($order instanceof ShopOrder) {
                        $pay_text .= '<b>Клиент:</b> '.($order->name ?? '—')."\n\r";
                        $pay_text .= '<b>Телефон:</b> '.($order->phone ?? '—')."\n\r";
                        $pay_text .= '<b>Email:</b> '.($order->email ?? '—')."\n\r";

                        if ($order->delivery) {
                            $pay_text .= '<b>Доставка:</b> '.($order->delivery->method ?? '—')."\n\r";
                            $pay_text .= '<b>Город:</b> '.($order->delivery->city ?? '—')."\n\r";
                            $pay_text .= '<b>Адрес:</b> '.($order->delivery->delivery_address ?? '—')."\n\r";
                            if ($order->delivery->price) {
                                $pay_text .= '<b>Цена доставки:</b> '.number_format((float) $order->delivery->price, 2, '.', '')." ₽\n\r";
                            }
                        }

                        $pay_text .= "\n\r<b>Товары:</b>\n\r";
                        foreach ($order->items as $item) {
                            $pay_text .= '• '.($item->product_name ?? $item->product_title ?? 'Товар').' ('.$item->product_sku.')';
                            $pay_text .= ' — '.$item->quantity.' шт. x '.number_format((float) $item->price, 2, '.', '').' ₽';
                            $pay_text .= ' = '.number_format((float) ($item->price * $item->quantity), 2, '.', '')." ₽\n\r";
                        }
                    }

                    $tgsender = new TelegramSendAction;
                    $tgsender->handle($pay_text);

                    Mail::to(config('cart.send_to'))->send(new PaymentStatusSend(
                        $order->id,
                        $someData['paymentId'],
                        $orderStatusText,
                        $amount,
                        $order instanceof ShopOrder ? $order : null
                    ));

                    Log::channel('pay')->info('Order status updated info: '.print_r($paymentInfo, true));
                    Log::channel('pay')->info('Order status updated: '.$order->id);

                } else {
                    Log::channel('pay')->info('Order not found: '.$someData['paymentId']);
                }
            } else {
                header('HTTP/1.1 400 Something went wrong (info)');
                exit();
            }

        } catch (\Exception $e) {
            header('HTTP/1.1 400 Something went wrong (all)');
            exit();
        }
    }

    protected function get_receipt($order, $tovars)
    {
        $receipt = [
            'tax_system_code' => 6,
            'items' => [],
            'customer' => [
                'full_name' => $this->getOrderField($order, 'name'),
                'email' => $this->getOrderField($order, 'email'),
                'phone' => '+7'.phone_format($this->getOrderField($order, 'phone')),
            ],
        ];

        foreach ($tovars as $product) {
            $receipt['items'][] = [
                'description' => $this->getItemTitle($product),
                'quantity' => $this->getItemQuantity($product),
                'vat_code' => 1,
                'payment_subject' => 'commodity',
                'payment_mode' => 'full_prepayment',
                'country_of_origin_code' => 'RU',
                'amount' => [
                    'value' => number_format((float) $this->getItemPrice($product), 2, '.', ''),
                    'currency' => 'RUB',
                ],
            ];
        }

        $deliveryPrice = $this->resolveDeliveryPrice($order);
        $deliveryCity = $this->resolveDeliveryCity($order);

        if ($deliveryPrice > 0) {
            $receipt['items'][] = [
                'description' => 'Доставка: '.$deliveryCity,
                'quantity' => 1,
                'vat_code' => 1,
                'payment_subject' => 'service',
                'payment_mode' => 'full_prepayment',
                'country_of_origin_code' => 'RU',
                'amount' => [
                    'value' => number_format((float) $deliveryPrice, 2, '.', ''),
                    'currency' => 'RUB',
                ],
            ];
        }

        return $receipt;
    }

    private function resolveDeliveryPrice($order): float
    {
        if ($order instanceof \App\Models\ShopOrder && $order->relationLoaded('delivery') && $order->delivery) {
            return (float) $order->delivery->price;
        }

        if ($order instanceof \App\Models\ShopOrder && method_exists($order, 'delivery')) {
            $delivery = $order->delivery()->first();

            if ($delivery) {
                return (float) $delivery->price;
            }
        }

        return (float) $this->getOrderField($order, 'delivery_price', 0);
    }

    private function resolveDeliveryCity($order): string
    {
        if ($order instanceof \App\Models\ShopOrder && $order->relationLoaded('delivery') && $order->delivery) {
            return (string) ($order->delivery->city ?? '');
        }

        if ($order instanceof \App\Models\ShopOrder && method_exists($order, 'delivery')) {
            $delivery = $order->delivery()->first();

            if ($delivery && $delivery->city) {
                return (string) $delivery->city;
            }
        }

        $deliveryInfo = $this->getOrderField($order, 'delivery_info', []);

        if (is_array($deliveryInfo) && isset($deliveryInfo['city'])) {
            return (string) $deliveryInfo['city'];
        }

        return '';
    }

    public function getOrderStatus(string $pay_id)
    {
        Log::channel('pay')->info('Get pay order staus: '.$pay_id);

        $client = new Client;
        $client->setAuth(config('yookassa.shop_id'), config('yookassa.secret_key'));

        try {
            return $client->getPaymentInfo($pay_id);
        } catch (\Exception $e) {
            Log::channel('pay')->error('Get pay order staus FAILD: '.$e->getMessage());

            return null;
        }
    }

    public function registerOrder($order, array $tovars)
    {
        $orderId = $this->getOrderField($order, 'id');
        $rawAmount = $this->getOrderField($order, 'amount', $this->getOrderField($order, 'total_summ', 0));
        $amount = number_format((float) $rawAmount, 2, '.', '');

        Log::channel('pay')->info('Pay create: '.$orderId.' amount: '.$amount);

        $client = new Client;
        $client->setAuth(config('yookassa.shop_id'), config('yookassa.secret_key'));

        $pay_info = [
            'amount' => [
                'value' => $amount,
                'currency' => 'RUB',
            ],
            'receipt' => $this->get_receipt($order, $tovars),
            'confirmation' => [
                'type' => 'redirect',
                'return_url' => route('bascet_thencs').'?order_id='.$orderId,
            ],
            'capture' => true,
            'description' => 'Заказ №'.$orderId,
            'metadata' => [
                'order_id' => $orderId,
            ],
        ];

        Log::channel('pay')->info('Pay info: '.json_encode($pay_info));

        $payment = $client->createPayment(
            $pay_info,
            uniqid('', true)
        );

        return $payment;
    }

    public function normalizeTovarsForPayment(array $tovars, float $baseSumm, float $discountSumm): array
    {
        if (empty($tovars)) {
            return $tovars;
        }

        $discount = max((int) round($discountSumm), 0);
        if ($discount <= 0) {
            return $tovars;
        }

        $base = max((int) round($baseSumm), 0);
        $targetSum = max($base - $discount, 0);

        $units = [];
        foreach ($tovars as $item) {
            $quantity = max((int) ($this->getItemQuantity($item) ?? 1), 1);
            $unitPrice = max((int) round((float) ($this->getItemPrice($item) ?? 0)), 0);

            for ($i = 0; $i < $quantity; $i++) {
                $units[] = [
                    'item' => $item,
                    'base_price' => $unitPrice,
                ];
            }
        }

        if (empty($units)) {
            return $tovars;
        }

        $baseUnitsSum = array_sum(array_column($units, 'base_price'));
        if ($baseUnitsSum <= 0) {
            return $tovars;
        }

        $allocated = [];
        $remainders = [];
        $allocatedSum = 0;

        foreach ($units as $index => $unit) {
            $rawValue = ($unit['base_price'] * $targetSum) / $baseUnitsSum;
            $floorValue = (int) floor($rawValue);

            $allocated[$index] = $floorValue;
            $remainders[$index] = $rawValue - $floorValue;
            $allocatedSum += $floorValue;
        }

        $leftToDistribute = max($targetSum - $allocatedSum, 0);
        arsort($remainders);

        foreach (array_keys($remainders) as $index) {
            if ($leftToDistribute <= 0) {
                break;
            }

            $allocated[$index] += 1;
            $leftToDistribute--;
        }

        $normalized = [];
        foreach ($units as $index => $unit) {
            $normalizedItem = $unit['item'];
            $normalizedItem['quentity'] = 1;
            $normalizedItem['price'] = $allocated[$index];
            $normalized[] = $normalizedItem;
        }

        return $normalized;
    }

    private function getOrderField($order, string $field, mixed $default = null): mixed
    {
        if (is_array($order)) {
            return $order[$field] ?? $default;
        }

        if (is_object($order) && method_exists($order, 'getAttribute')) {
            return $order->getAttribute($field) ?? $default;
        }

        return $default;
    }

    private function getItemTitle(array $item): string
    {
        return $item['tovar_content']['title'] ?? $item['product_title'] ?? $item['product_name'] ?? '';
    }

    private function getItemQuantity(array $item): int
    {
        return (int) ($item['quentity'] ?? $item['quantity'] ?? 1);
    }

    private function getItemPrice(array $item): int
    {
        return (int) round((float) ($item['price'] ?? 0));
    }
}
