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
            @apply-promocode="applyPromocode"
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
import CartCheckoutForm from "./CartCheckoutForm.vue";
import CartItemsList from "./CartItemsList.vue";
import CartOrderSummary from "./CartOrderSummary.vue";

const noPhotoUrl = "img/noPhoto.jpg";
const bascetList = ref([]);
const loadet = ref(false);
const count = ref(0);
const subtotal = ref(0);
const show_bascet = ref(false);
const payType = ref(1);
const deliveryMethod = ref("");
const deliveryPrice = ref(0);
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

    return axios
        .post("/promocod/verify", {
            _token: token,
            promocode: promoCode,
            cart_sum: subtotal.value,
        })
        .then((response) => {
            if (requestId !== promoRequestId.value) {
                return false;
            }

            promoApplied.value = true;
            promoDiscount.value = Number(response.data.discount || 0);
            appliedPromoCode.value = response.data.promo_code || promoCode;
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

    for (const item of bascetList.value) {
        count.value += Number(item.quentity);
        subtotal.value += Number(item.quentity) * Number(item.price);
    }

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

    // if (bascetInfo.city == "")
    //     errorList.value.push("Поле 'Город' не заполнено");

    // if (bascetInfo.street == "")
    //     errorList.value.push("Поле 'Улица' не заполнено");

    // if (bascetInfo.home == "")
    //     errorList.value.push("Поле 'Дом' не заполнено");

    // if (bascetInfo.postindex == "")
    //     errorList.value.push("Поле 'Почтовый индекс' не заполнено");

    if (errorList.value.length != 0) return;

    loadet.value = true;

    try {
        const response = await axios.post("/bascet/send", {
            _token: token,
            fio: bascetInfo.fio,
            email: bascetInfo.email,
            phone: bascetInfo.phone,
            adress: bascetInfo.adress,
            comment: bascetInfo.comment,
            count: count.value,
            promo_code: appliedPromoCode.value,
            promo_code_discount: promoDiscount.value,
            amount: subtotal.value + deliveryPrice.value - promoDiscount.value,
            delivery: deliveryMethod.value,
            pay: payType.value == 1 ? "Ю-касса" : "Перевод на карту",
            tovars: bascetList.value,
        });

        console.log(response);

        if (
            response.data.pay_info != null &&
            response.data.pay_info.confirmation.confirmation_url !== undefined
        ) {
            console.log(response.data.pay_info);

            document.location.href =
                response.data.pay_info.confirmation.confirmation_url;
        } else {
            console.log(response.data.pay_info);
            document.location.href = "/bascet/thencs";
        }
    } catch (error) {
        console.log(error);
    } finally {
        loadet.value = false;
    }
};

const updateItem = (item) => {
    axios
        .post("/bascet/update", {
            _token: token,
            product_id: item.product_id,
            count: item.quentity,
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
    axios
        .delete("/bascet/clear", {
            data: {
                _token: token,
            },
        })
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
    axios
        .delete("/bascet/delete", {
            data: {
                _token: token,
                product_id: item.product_id,
            },
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
    axios
        .get("/bascet/get")
        .then((response) => {
            bascetList.value = response.data.position;
            console.log(bascetList.value);
            updateBascet();
            show_bascet.value = true;
            syncCounterInHeader();
        })
        .catch((error) => console.log(error));
});
</script>

<style></style>
