<template>
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
                v-mask="{ mask: '+7 (NNN) NNN-NN-NN', model: 'cpf' }"
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
                @click.prevent="$emit('apply-promocode')"
                class="button"
                type="submit"
            >
                Применить
            </button>

            <p v-if="promoMessage" :class="['promo_message', promoMessageType]">
                {{ promoMessage }}
            </p>
            <p v-if="promoDirty && promoApplied" class="promo_message info">
                Сумма корзины изменилась, скидка будет пересчитана.
            </p>

            <ul v-show="errorList.length != 0" class="errors_list">
                <li v-for="item in errorList" :key="item">{{ item }}</li>
            </ul>

            <!-- <div class="page_manager_info in_cart">
                <p>
                    Уточнить цену и наличие товара вы можете по телефону
                    <br /><a href="tel:+79510849233">+7 (951) 084-92-33</a>
                    Пн. - Пт. с 9 до 18.00 по МСК
                </p>
            </div> -->

            <div class="pay_information">
                <p>
                    После оформления вы автоматически перейдете на страницу
                    оплаты сервиса ЮKassa, где сможете выбрать удобный способ
                    оплаты.
                </p>
                <div class="pay_icons">
                    <img
                        :src="`${assetUrl}images/icons/pay_system/ykassa.svg`"
                        alt="pay icons"
                    />
                    <img
                        :src="`${assetUrl}images/icons/pay_system/sber-pay.svg`"
                        alt="pay icons"
                    />
                    <img
                        :src="`${assetUrl}images/icons/pay_system/sbp.svg`"
                        alt="pay icons"
                    />
                </div>
            </div>

            <button
                @click.prevent="$emit('submit-order')"
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
</template>

<script setup>
defineProps({
    bascetInfo: {
        type: Object,
        required: true,
    },
    promoMessage: {
        type: String,
        required: true,
    },
    promoMessageType: {
        type: String,
        required: true,
    },
    promoDirty: {
        type: Boolean,
        required: true,
    },
    promoApplied: {
        type: Boolean,
        required: true,
    },
    errorList: {
        type: Array,
        required: true,
    },
    loadet: {
        type: Boolean,
        required: true,
    },
});
const assetUrl = window.Laravel?.assetUrl || "/";
const storageUrl = window.Laravel?.storageUrl || "/storage/";

defineEmits(["apply-promocode", "submit-order"]);
</script>

<style></style>
