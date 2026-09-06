<template>
    <div v-show="prices.length > 1" class="price-selector">
        <button
            v-for="(item, index) in prices"
            :key="item.id"
            type="button"
            class="price-selector__btn"
            :class="{
                'price-selector__btn--active': selectedIndex === index,
                'price-selector__btn--disabled': !hasStock(item),
            }"
            :disabled="!hasStock(item)"
            :title="hasStock(item) ? '' : 'Нет в наличии'"
            @click="select(index)"
        >
            {{ item.value }}
        </button>
    </div>
</template>

<script>
import { ref, onMounted } from "vue";

export default {
    props: {
        prices: Array,
        sku: String,
    },
    emits: ["select"],
    setup(props, { emit }) {
        const hasStock = (item) => Number(item?.count) > 0;

        const initialIndex = (() => {
            const skuIdx = props.prices.findIndex((p) => p.sku === props.sku);
            if (skuIdx >= 0 && hasStock(props.prices[skuIdx])) {
                return skuIdx;
            }

            const firstAvailable = props.prices.findIndex(hasStock);
            return firstAvailable >= 0 ? firstAvailable : 0;
        })();

        let selectedIndex = ref(initialIndex);

        const select = (index) => {
            if (!hasStock(props.prices[index])) {
                return;
            }

            selectedIndex.value = index;
            emit("select", index);
        };

        onMounted(() => {
            emit("select", selectedIndex.value);
        });

        return {
            selectedIndex,
            select,
            hasStock,
        };
    },
};
</script>

<style scoped>
.price-selector__btn--disabled,
.price-selector__btn--disabled:hover {
    background-color: #f5f5f5;
    border-color: #e0e0e0;
    color: #b0b0b0;
    cursor: not-allowed;
    text-decoration: line-through;
}

.price-selector__btn--active.price-selector__btn--disabled {
    background-color: #f5f5f5;
    border-color: #e0e0e0;
    color: #b0b0b0;
}
</style>
