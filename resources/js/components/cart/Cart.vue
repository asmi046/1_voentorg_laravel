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
            <div class="control">
                <a
                    @click.prevent="clearBascet()"
                    class="clear_bascet_btn"
                    href="#"
                >
                    <svg class="cart_icon cart_icon_loader">
                        <use xlink:href="#cart_clear"></use>
                    </svg>

                    <span>Очистить корзину</span>
                </a>
            </div>

            <div class="tovar_list">
                <div
                    v-for="(item, index) in bascetList"
                    :key="item.product_id"
                    class="tovar"
                >
                    <div class="tl-side left-side">
                        <div class="tovar_all_blk picture_blk">
                            <img
                                v-if="item.tovar_content.img != ''"
                                :src="item.tovar_content.img"
                                alt=""
                            />
                            <img v-else else :src="noPhotoUrl" alt="" />
                        </div>
                        <div class="tovar_all_blk name_blk">
                            <h2>
                                <a
                                    target="_blank"
                                    :href="`/product/${item.tovar_content.slug}`"
                                >
                                    {{ item.tovar_content.title }}
                                    {{ item.tovar_data.volume }}
                                    {{ item.tovar_data.ed_izm }}
                                </a>
                            </h2>
                            <p>
                                Артикул: {{ item.product_sku }} /
                                {{ item.product_id }}
                            </p>
                        </div>
                    </div>

                    <div class="tl-side right-side">
                        <div class="tovar_all_blk price_blk">
                            <span class="rub price_formator">{{
                                Number(item.price).toLocaleString("ru-RU")
                            }}</span>
                        </div>
                        <div class="tovar_all_blk couint_blk">
                            <div class="number_wrapper">
                                <span
                                    @click="changeItemQuantity(item, -1)"
                                    class="number_btn val_down"
                                    >-</span
                                >
                                <input type="number" :value="item.quentity" />
                                <span
                                    @click="changeItemQuantity(item, 1)"
                                    class="number_btn val_upp"
                                    >+</span
                                >
                            </div>
                        </div>
                        <div class="tovar_all_blk summ_blk">
                            <span class="rub price_formator"
                                >{{
                                    Number(
                                        parseFloat(item.quentity) *
                                            parseFloat(item.price),
                                    ).toLocaleString("ru-RU")
                                }}
                                <span class="rub_symbol">₽</span></span
                            >
                        </div>
                        <div class="tovar_all_blk dll_blk">
                            <span
                                @click.prevent="deleteElement(item, index)"
                                title="Удалить товар"
                            ></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="itogo">
                <div class="itogo_price_count">
                    <div class="itogo_row">
                        <span class="text"
                            >Товары (<span>{{ count }}</span
                            >)</span
                        >
                        <span class="razd"></span>
                        <span class="p_price rub price_formator"
                            >{{ Number(subtotal).toLocaleString("ru-RU") }}
                            <span class="rub_symbol">₽</span>
                        </span>
                    </div>

                    <div v-if="deliveryPrice != 0" class="itogo_row">
                        <span class="text">Доставка</span>
                        <span class="razd"></span>
                        <span class="p_price rub price_formator"
                            >{{
                                Number(deliveryPrice).toLocaleString("ru-RU")
                            }}₽ <span class="rub_symbol">₽</span>
                        </span>
                    </div>

                    <div v-if="promoApplied" class="itogo_row">
                        <span class="text">Скидка по промокоду</span>
                        <span class="razd"></span>
                        <span class="p_price rub price_formator"
                            >-{{
                                Number(promoDiscount).toLocaleString("ru-RU")
                            }}
                            <span class="rub_symbol">₽</span>
                        </span>
                    </div>

                    <div class="itogo_row itogo_row_final">
                        <span class="text">Итого</span>1
                        <span class="razd"></span>
                        <span class="p_price rub price_formator"
                            >{{ Number(finalTotal).toLocaleString("ru-RU") }}
                            <span class="rub_symbol">₽</span>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="bascet_form">
            <h2>Контактные данные</h2>
            <form action="GET">
                <input
                    v-model="bascetInfo.fio"
                    name="fio"
                    type="text"
                    placeholder="Фамилия, Имя*"
                />
                <input
                    v-model="bascetInfo.email"
                    name="email"
                    type="email"
                    placeholder="e-mail"
                />
                <input
                    v-model="bascetInfo.phone"
                    v-mask="{ mask: '+N (NNN) NNN-NN-NN', model: 'cpf' }"
                    name="phone"
                    type="text"
                    placeholder="Телефон*"
                />
                <textarea
                    v-model="bascetInfo.comment"
                    name="comment"
                    placeholder="Комментарий"
                ></textarea>

                <h3 class="cart_h3">Промокод</h3>
                <input
                    v-model="bascetInfo.promokod"
                    name="promo"
                    type="text"
                    placeholder="Введите промокод"
                />
                <button
                    @click.prevent="applyPromocode()"
                    class="button"
                    type="submit"
                >
                    Применить
                </button>

                <p
                    v-if="promoMessage"
                    :class="['promo_message', promoMessageType]"
                >
                    {{ promoMessage }}
                </p>

                <!--
                <h2>Адрес доставки</h2>
                <input @change="calcDeliveryPrice" v-model="bascetInfo.city" name="city" type="text" placeholder="Город*">
                <input @change="calcDeliveryPrice" v-model="bascetInfo.street" name="street" type="text" placeholder="Улица*">
                <input @change="calcDeliveryPrice" v-model="bascetInfo.home" name="home" type="text" placeholder="Дом*">
                <input @change="calcDeliveryPrice" v-model="bascetInfo.postindex" name="postindex" type="text" placeholder="Почтовый индекс*">
                -->

                <!-- <textarea v-model="bascetInfo.adress" name="adress" placeholder="Адрес"></textarea> -->

                <!-- <select v-model="deliveryMethod" name="delivery_method" >
                    <option value="" selected disabled>Выберите спопоб доставки</option>
                    <option value="Почта России">Почта России</option>
                    <option value="Курьером до двери">Курьером до двери</option>
                    <option value="Транспортной компантей до пункта выдачи">Транспортной компантей до пункта выдачи</option>
                </select> -->

                <!-- <h2>Способ оплаты</h2>
                <pay-selector v-model="payType"></pay-selector>
                -->

                <ul v-show="errorList.length != 0" class="errors_list">
                    <li v-for="item in errorList" :key="item">{{ item }}</li>
                </ul>

                <div class="page_manager_info in_cart">
                    <p>
                        Уточнить цену и наличие товара вы можете по телефону
                        <br /><a href="tel:+79510849233">+7 (951) 084-92-33</a>
                        Пн. - Пт. с 9 до 18.00 по МСК
                    </p>
                </div>

                <button
                    @click.prevent="sendBascet()"
                    class="button"
                    type="submit"
                >
                    Оформить
                </button>
                <span
                    :class="{ active: loadet }"
                    class="btnLoaderCart shoved"
                ></span>
                <p class="policy">
                    Заполняя данную форму и отправляя заказ вы соглашаетесь с
                    <a href="/policy">политикой конфиденциальности</a>
                </p>
            </form>
        </div>
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
    promoApplied.value = false;
    promoDiscount.value = 0;
    appliedPromoCode.value = "";
    promoMessage.value = "";
    promoMessageType.value = "";
};

const showPromoSuccess = (message) => {
    promoMessage.value = message;
    promoMessageType.value = "success";
};

const showPromoError = (message) => {
    promoMessage.value = message;
    promoMessageType.value = "error";
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
        recalculatePromocode();
    }
};

const applyPromocode = () => {
    const promoCode = (bascetInfo.promokod || "").trim();

    if (promoCode === "") {
        resetPromocode();
        showPromoError("Введите промокод.");
        return;
    }

    axios
        .post("/promocod/verify", {
            _token: token,
            promocode: promoCode,
            cart_sum: subtotal.value,
        })
        .then((response) => {
            promoApplied.value = true;
            promoDiscount.value = Number(response.data.discount || 0);
            appliedPromoCode.value = response.data.promo_code || promoCode;
            showPromoSuccess("Промокод успешно применен.");
        })
        .catch((error) => {
            resetPromocode();
            showPromoError(
                error?.response?.data?.message ||
                    "Не удалось применить промокод.",
            );
        });
};

const recalculatePromocode = () => {
    axios
        .post("/promocod/verify", {
            _token: token,
            promocode: appliedPromoCode.value,
            cart_sum: subtotal.value,
        })
        .then((response) => {
            promoApplied.value = true;
            promoDiscount.value = Number(response.data.discount || 0);
            appliedPromoCode.value =
                response.data.promo_code || appliedPromoCode.value;
            showPromoSuccess("Скидка по промокоду пересчитана.");
        })
        .catch((error) => {
            resetPromocode();
            showPromoError(
                error?.response?.data?.message ||
                    "Промокод больше не действует.",
            );
        });
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

const sendBascet = () => {
    console.log(deliveryMethod.value);

    errorList.value = [];

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
    axios
        .post("/bascet/send", {
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
        })
        .then((response) => {
            console.log(response);
            loadet.value = false;

            // if (payType.value == 1)
            //     document.location.href=response.data.pay_url
            // else
            //     document.location.href="/bascet/thencs"

            document.location.href = "/bascet/thencs";
        })
        .catch((error) => console.log(error));
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
            _token: token,
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
