<template>
    <div class="tovar">
        <div class="tl-side left-side">
            <div class="tovar_all_blk picture_blk">
                <img
                    v-if="item.product?.img"
                    :src="item.product.img"
                    :alt="item.product?.title ?? item.product_sku"
                />
                <img v-else :src="noPhotoUrl" alt="" />
            </div>
            <div class="tovar_all_blk name_blk">
                <h2>
                    <a
                        target="_blank"
                        :href="`/product/${item.product?.slug ?? item.product_sku}`"
                    >
                        {{ item.product?.title ?? item.product_sku }}
                    </a>
                </h2>
                <p class="tovar_sku">Артикул: {{ item.product_sku }}</p>
                <p v-if="hasSize" class="tovar_size">
                    Размер: <span>{{ item.variant.value }}</span>
                </p>
                <p v-if="dimensionsText" class="tovar_dims">
                    Габариты: {{ dimensionsText }}
                </p>
            </div>
        </div>

        <div class="tl-side right-side">
            <div class="tovar_all_blk price_blk">
                <span class="rub price_formator">{{
                    Number(item.price).toLocaleString("ru-RU")
                }}</span>
                <span
                    v-if="item.old_price && item.old_price > item.price"
                    class="rub price_formator price_old"
                >
                    {{ Number(item.old_price).toLocaleString("ru-RU") }}
                </span>
            </div>
            <div class="tovar_all_blk couint_blk">
                <div class="number_wrapper">
                    <span
                        @click="$emit('change-item-quantity', item, -1)"
                        class="number_btn val_down"
                        >-</span
                    >
                    <input type="number" :value="item.quantity" />
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
                            parseFloat(item.quantity) * parseFloat(item.price),
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
import { computed } from "vue";

const props = defineProps({
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

const hasSize = computed(() => {
    const value = props.item?.variant?.value;
    return value !== null && value !== undefined && value !== "" && value !== "-";
});

const dimensionsText = computed(() => {
    const p = props.item?.product;
    if (!p) return "";

    const parts = [];
    if (p.length) parts.push(`${p.length} см`);
    if (p.width) parts.push(`${p.width} см`);
    if (p.height) parts.push(`${p.height} см`);

    return parts.length ? `${parts.join(" × ")}${p.weight ? `, ${p.weight} г` : ""}` : "";
});
</script>

<style scoped>
.tovar_sku,
.tovar_size,
.tovar_dims {
    margin: 2px 0;
    font-size: 13px;
    color: #555;
}

.tovar_size span {
    font-weight: 600;
    color: #222;
}

.price_old {
    display: block;
    font-size: 12px;
    color: #999;
    text-decoration: line-through;
}
</style>
