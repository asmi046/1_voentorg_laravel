<template>
    <div class="tovar">
        <div class="tl-side left-side">
            <div class="tovar_all_blk picture_blk">
                <img
                    v-if="item.tovar_content.img != ''"
                    :src="item.tovar_content.img"
                    alt=""
                />
                <img v-else :src="noPhotoUrl" alt="" />
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
                <p>Артикул: {{ item.product_sku }} / {{ item.product_id }}</p>
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
                        @click="$emit('change-item-quantity', item, -1)"
                        class="number_btn val_down"
                        >-</span
                    >
                    <input type="number" :value="item.quentity" />
                    <span
                        @click="$emit('change-item-quantity', item, 1)"
                        class="number_btn val_upp"
                        >+</span
                    >
                </div>
            </div>
            <div class="tovar_all_blk summ_blk">
                <span class="rub price_formator"
                    >{{
                        Number(
                            parseFloat(item.quentity) * parseFloat(item.price),
                        ).toLocaleString("ru-RU")
                    }}
                    <span class="rub_symbol">₽</span></span
                >
            </div>
            <div class="tovar_all_blk dll_blk">
                <span
                    @click.prevent="$emit('delete-element', item, index)"
                    title="Удалить товар"
                ></span>
            </div>
        </div>
    </div>
</template>

<script setup>
defineProps({
    item: {
        type: Object,
        required: true,
    },
    index: {
        type: Number,
        required: true,
    },
    noPhotoUrl: {
        type: String,
        required: true,
    },
});

defineEmits(["change-item-quantity", "delete-element"]);
</script>

<style></style>
