<template>
    <div v-show="!show_bascet" class="bascet_lader">
        <span>
            <svg class="cart_icon cart_icon_loader">
                <use xlink:href="#loader"></use>
            </svg>
        </span>
        <p>Загружаем корзину...</p>
    </div>
    <div v-show="bascetList.length != 0" class="bascet__">
        <div class="bascet_tovar">
            <CartItemsList
                :bascet-list="bascetList"
                :no-photo-url="noPhotoUrl"
                @clear-bascet="clearBascet"
                @change-item-quantity="changeItemQuantity"
                @delete-element="deleteElement"
            />

            <CartOrderSummary
                :count="count"
                :subtotal="subtotal"
                :delivery-price="deliveryPrice"
                :promo-applied="promoApplied"
                :promo-discount="promoDiscount"
                :final-total="finalTotal"
            />
        </div>

        <CartCheckoutForm
            :bascet-info="bascetInfo"
            :promo-message="promoMessage"
            :promo-message-type="promoMessageType"
            :promo-dirty="promoDirty"
            :promo-applied="promoApplied"
            :error-list="errorList"
            :loadet="loadet"
            :parcel-weight-grams="parcelWeightGrams"
            @apply-promocode="applyPromocode"
            @delivery-change="onDeliveryChange"
            @submit-order="sendBascet"
        />
    </div>
    <div class="empty_bascet" v-show="show_bascet && bascetList.length == 0">
        <svg class="cart_icon">
            <use xlink:href="#empty_cart"></use>
        </svg>
        <h3>Ваша корзина пуста</h3>
        <p>Жмите на значек корзиныи добавляйте товар!</p>
    </div>
</template>

<script setup>
import { computed, onMounted, reactive, ref, watch } from "vue";
import CartCheckoutForm from "./checkout/CartCheckoutForm.vue";
import CartItemsList from "./items/CartItemsList.vue";
import CartOrderSummary from "./items/CartOrderSummary.vue";
import * as cartApi from "@/api/cart";
import * as promocodeApi from "@/api/promocode";

const noPhotoUrl = "img/noPhoto.jpg";
const bascetList = ref([]);
const loadet = ref(false);
const count = ref(0);
const subtotal = ref(0);
const show_bascet = ref(false);
const payType = ref(1);
const deliveryMethod = ref("");
const deliveryPrice = ref(0);
const parcelWeightGrams = ref(0);
const deliveryData = ref({
    deliveryMethod: "Самовывоз",
    transportCompany: null,
    deliveryType: "pickup",
    deliveryDateRange: null,
    selectedPickupPoint: null,
    selectedCity: null,
    deliveryAddress: "",
    apartment: "",
    deliveryPrice: 0,
});
const errorList = ref([]);
const promoApplied = ref(false);
const promoDiscount = ref(0);
const promoMessage = ref("");
const promoMessageType = ref("");
const appliedPromoCode = ref("");
const promoDirty = ref(false);
const promoRecalcTimer = ref(null);
const promoRequestId = ref(0);
const bascetInfo = reactive({
    fio: "",
    email: "",
    phone: "",
    adress: "",
    city: "",
    street: "",
    home: "",
    postindex: "",
    comment: "",
    promokod: "",
});

console.log(bascetList);

const finalTotal = computed(() => {
    return Math.max(
        subtotal.value + deliveryPrice.value - promoDiscount.value,
        0,
    );
});

const token = document.querySelector('meta[name="_token"]')?.content || "";

const syncCounterInHeader = () => {
    const bascetCounter = document.querySelectorAll(".bascet_counter");
    for (const elem of bascetCounter) {
        elem.innerHTML = count.value;
    }
};

const resetPromocode = () => {
    if (promoRecalcTimer.value) {
        clearTimeout(promoRecalcTimer.value);
        promoRecalcTimer.value = null;
    }

    promoApplied.value = false;
    promoDiscount.value = 0;
    appliedPromoCode.value = "";
    promoMessage.value = "";
    promoMessageType.value = "";
    promoDirty.value = false;
};

const showPromoSuccess = (message) => {
    promoMessage.value = message;
    promoMessageType.value = "success";
};

const showPromoError = (message) => {
    promoMessage.value = message;
    promoMessageType.value = "error";
};

const verifyPromocode = (promoCode, successMessage) => {
    const requestId = ++promoRequestId.value;

    return promocodeApi
        .verifyPromocode({
            _token: token,
            promocode: promoCode,
            cart_sum: subtotal.value,
        })
        .then((data) => {
            if (requestId !== promoRequestId.value) {
                return false;
            }

            promoApplied.value = true;
            promoDiscount.value = Number(data.discount || 0);
            appliedPromoCode.value = data.promo_code || promoCode;
            promoDirty.value = false;
            showPromoSuccess(successMessage);

            return true;
        })
        .catch((error) => {
            if (requestId !== promoRequestId.value) {
                return false;
            }

            resetPromocode();
            showPromoError(
                error?.response?.data?.message ||
                    "Не удалось применить промокод.",
            );

            return false;
        });
};

const schedulePromocodeRecalculation = () => {
    if (!promoApplied.value || !appliedPromoCode.value) {
        return;
    }

    promoDirty.value = true;

    if (promoRecalcTimer.value) {
        clearTimeout(promoRecalcTimer.value);
    }

    promoRecalcTimer.value = setTimeout(() => {
        recalculatePromocode();
    }, 700);
};

const updateBascet = () => {
    count.value = 0;
    subtotal.value = 0;
    parcelWeightGrams.value = 0;

    const DEFAULT_WEIGHT_GRAMS = 300;

    const normalizeWeightToGrams = (weight) => {
        const numeric = Number(weight);

        if (!Number.isFinite(numeric) || numeric <= 0) {
            return DEFAULT_WEIGHT_GRAMS;
        }

        // If weight looks like kilograms (e.g. 0.7, 3), convert to grams.
        if (numeric < 50) {
            return Math.round(numeric * 1000);
        }

        return Math.round(numeric);
    };

    for (const item of bascetList.value) {
        const quantity = Number(item.quentity ?? item.quantity) || 0;
        const itemWeight = item?.tovar_content?.weight;

        count.value += quantity;
        subtotal.value += quantity * Number(item.price);
        parcelWeightGrams.value +=
            normalizeWeightToGrams(itemWeight) * quantity;
    }

    console.log("Parcel weight (g):", parcelWeightGrams.value);

    syncCounterInHeader();

    if (count.value === 0) {
        resetPromocode();
        return;
    }

    if (promoApplied.value && appliedPromoCode.value) {
        schedulePromocodeRecalculation();
    }
};

const applyPromocode = () => {
    const promoCode = (bascetInfo.promokod || "").trim();

    if (promoCode === "") {
        resetPromocode();
        showPromoError("Введите промокод.");
        return;
    }

    verifyPromocode(promoCode, "Промокод успешно применен.");
};

const recalculatePromocode = () => {
    if (!appliedPromoCode.value) {
        return Promise.resolve(false);
    }

    return verifyPromocode(
        appliedPromoCode.value,
        "Скидка по промокоду пересчитана.",
    );
};

const onDeliveryChange = (payload) => {
    console.log("Delivery change payload:", payload);
    deliveryData.value = {
        deliveryMethod: payload?.deliveryMethod || "",
        transportCompany: payload?.transportCompany || null,
        deliveryDateRange: payload?.deliveryDateRange || null,
        deliveryType: payload?.deliveryType || "",
        selectedPickupPoint: payload?.selectedPickupPoint || null,
        selectedCity: payload?.selectedCity || null,
        deliveryAddress: payload?.deliveryAddress || "",
        apartment: payload?.apartment || "",
        deliveryPrice:
            payload?.deliveryPrice === null ||
            payload?.deliveryPrice === undefined ||
            payload?.deliveryPrice === ""
                ? null
                : Number(payload.deliveryPrice),
        tariff: payload?.tariff || null,
    };

    deliveryMethod.value = deliveryData.value.deliveryMethod;

    const numericDeliveryPrice = Number(deliveryData.value.deliveryPrice);
    deliveryPrice.value = Number.isFinite(numericDeliveryPrice)
        ? numericDeliveryPrice
        : 0;
};

const calcDeliveryPrice = () => {
    if (
        bascetInfo.city != "" &&
        bascetInfo.street != "" &&
        bascetInfo.home != "" &&
        bascetInfo.postindex != ""
    ) {
        if (subtotal.value > 3000) {
            deliveryPrice.value = 0;
            return;
        }

        axios
            .get("/delivery_calc", {
                params: {
                    city: bascetInfo.city,
                    street: bascetInfo.street,
                    home: bascetInfo.home,
                    postindex: bascetInfo.postindex,
                    price: subtotal.value,
                },
            })
            .then((response) => {
                deliveryPrice.value = parseFloat(response.data.pricing_total);
                console.log(deliveryPrice.value);
                console.log(response.data);
            })
            .catch((error) => console.log(error));
    }
};

const sendBascet = async () => {
    console.log(deliveryMethod.value);

    errorList.value = [];

    if (promoApplied.value && promoDirty.value && appliedPromoCode.value) {
        const promoValid = await recalculatePromocode();

        if (!promoValid) {
            return;
        }
    }

    if (subtotal.value < 500)
        errorList.value.push("Минимальная сумма заказа 500 р.");

    if (bascetInfo.fio == "") errorList.value.push("Поле 'Имя' не заполнено");

    if (bascetInfo.phone == "")
        errorList.value.push("Поле 'Телефон' не заполнено");

    if (errorList.value.length != 0) return;

    loadet.value = true;

    try {
        const selectedCity = deliveryData.value.selectedCity || {};
        const selectedPoint = deliveryData.value.selectedPickupPoint || {};

        const formData = {
            _token: token,
            name: bascetInfo.fio,
            email: bascetInfo.email,
            phone: bascetInfo.phone,
            comment: bascetInfo.comment,
            promo_code: appliedPromoCode.value || undefined,
            delivery: {
                provider: deliveryData.value.transportCompany || undefined,
                method: deliveryData.value.deliveryType || undefined,
                price: deliveryData.value.deliveryPrice ?? undefined,
                tariff:
                    deliveryData.value.tariff != null
                        ? typeof deliveryData.value.tariff === "string"
                            ? deliveryData.value.tariff
                            : JSON.stringify(deliveryData.value.tariff)
                        : undefined,
                delivery_date_range:
                    deliveryData.value.deliveryDateRange || undefined,
                city:
                    selectedCity.name ||
                    selectedCity.city ||
                    selectedCity ||
                    undefined,
                pickup_point_id:
                    selectedPoint.id || selectedPoint.code || undefined,
                pickup_point_address: selectedPoint.address || undefined,
                delivery_address:
                    deliveryData.value.deliveryAddress || undefined,
                apartment: deliveryData.value.apartment || undefined,
                raw_data: deliveryData.value,
            },
            items: bascetList.value.map((item) => ({
                product_sku: item.product_sku,
                quantity: Number(item.quantity ?? item.quentity) || 1,
            })),
        };

        const response = await cartApi.checkout(formData);
        console.log(response);
        if (
            response.pay_info != null &&
            response.pay_info.confirmation &&
            response.pay_info.confirmation.confirmation_url !== undefined
        ) {
            console.log(response.pay_info);

            document.location.href =
                response.pay_info.confirmation.confirmation_url;
        } else {
            console.log(response.pay_info);
            // document.location.href = "/bascet/thencs";
        }
    } catch (error) {
        if (error?.response?.data?.errors) {
            const backendErrors = error.response.data.errors;
            for (const fieldMessages of Object.values(backendErrors)) {
                if (Array.isArray(fieldMessages)) {
                    errorList.value.push(...fieldMessages);
                } else if (typeof fieldMessages === 'string') {
                    errorList.value.push(fieldMessages);
                }
            }
        } else if (error?.response?.data?.message) {
            errorList.value.push(error.response.data.message);
        } else {
            errorList.value.push('Произошла ошибка при оформлении заказа.');
        }

        console.log(error);
    } finally {
        loadet.value = false;
    }
};

const updateItem = (item) => {
    cartApi
        .updateCartItem({
            _token: token,
            product_sku: item.product_sku,
            quantity: Number(item.quentity),
        })
        .then(() => {
            syncCounterInHeader();
        })
        .catch((error) => console.log(error));
};

const changeItemQuantity = (item, delta) => {
    item.quentity = Math.max(1, Number(item.quentity) + delta);
    updateBascet();
    updateItem(item);
};

const clearBascet = () => {
    cartApi
        .clearCart({ _token: token })
        .then(() => {
            count.value = 0;
            subtotal.value = 0;
            bascetList.value = [];
            show_bascet.value = true;
            resetPromocode();
            syncCounterInHeader();
        })
        .catch((error) => console.log(error));
};

const deleteElement = (item, index) => {
    cartApi
        .deleteCartItem({
            _token: token,
            product_sku: item.product_sku,
        })
        .then(() => {
            item.quentity = 0;
            bascetList.value.splice(index, 1);
            updateBascet();
        })
        .catch((error) => console.log(error));
};

watch(
    () => bascetInfo.promokod,
    (newValue) => {
        if (promoApplied.value && newValue.trim() !== appliedPromoCode.value) {
            if (promoRecalcTimer.value) {
                clearTimeout(promoRecalcTimer.value);
                promoRecalcTimer.value = null;
            }

            resetPromocode();

            if (newValue.trim() !== "") {
                showPromoError("Промокод изменен, примените его заново.");
            }
        }
    },
);

onMounted(() => {
    show_bascet.value = false;
    cartApi
        .getCart()
        .then((data) => {
            bascetList.value = data.position;
            console.log(bascetList.value);
            updateBascet();
            show_bascet.value = true;
            syncCounterInHeader();
        })
        .catch((error) => console.log(error));
});
</script>

<style></style>
