<template>
    <div v-show="prices.length > 1" class="price-selector">
        <button
            v-for="(item, index) in prices"
            :key="item.id"
            type="button"
            class="price-selector__btn"
            :class="{ 'price-selector__btn--active': selectedIndex === index }"
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
        const initialIndex = (() => {
            const idx = props.prices.findIndex((p) => p.sku === props.sku);
            return idx >= 0 ? idx : 0;
        })();

        let selectedIndex = ref(initialIndex);

        const select = (index) => {
            selectedIndex.value = index;
            emit("select", index);
        };

        onMounted(() => {
            emit("select", selectedIndex.value);
        });

        return {
            selectedIndex,
            select,
        };
    },
};
</script>