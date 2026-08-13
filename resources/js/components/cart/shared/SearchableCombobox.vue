<template>
    <div ref="root" class="searchable_combobox">
        <input
            v-model="query"
            type="text"
            class="searchable_combobox__input"
            :disabled="isDisabled"
            :placeholder="resolvedPlaceholder"
            autocomplete="off"
            @focus="openDropdown"
            @input="onInput"
            @keyup="onKeyup"
            @compositionstart="onCompositionStart"
            @compositionend="onCompositionEnd"
            @keydown.down.prevent="moveHighlight(1)"
            @keydown.up.prevent="moveHighlight(-1)"
            @keydown.enter.prevent="selectHighlighted"
            @keydown.esc.prevent="closeDropdown"
        />

        <ul v-if="isDropdownOpen" class="searchable_combobox__list">
            <li v-if="!filteredItems.length" class="searchable_combobox__empty">
                {{ emptyText }}
            </li>
            <li
                v-for="(item, index) in filteredItems"
                v-else
                :key="getValue(item)"
                class="searchable_combobox__item"
                :class="{ active: index === highlightedIndex }"
                @mousedown.prevent="selectItem(item)"
            >
                {{ getLabel(item) }}
            </li>
        </ul>
    </div>
</template>

<script setup>
import { computed, onBeforeUnmount, onMounted, ref, watch } from "vue";

const props = defineProps({
    modelValue: {
        type: [String, Number],
        default: "",
    },
    items: {
        type: Array,
        default: () => [],
    },
    loading: {
        type: Boolean,
        default: false,
    },
    disabled: {
        type: Boolean,
        default: false,
    },
    placeholder: {
        type: String,
        default: "Начните вводить",
    },
    loadingPlaceholder: {
        type: String,
        default: "Загрузка...",
    },
    emptyText: {
        type: String,
        default: "Ничего не найдено",
    },
    labelKey: {
        type: String,
        default: "name",
    },
    valueKey: {
        type: String,
        default: "code",
    },
    maxResults: {
        type: Number,
        default: 100,
    },
});

const emit = defineEmits(["update:modelValue", "change"]);

const root = ref(null);
const query = ref("");
const isDropdownOpen = ref(false);
const highlightedIndex = ref(-1);
const isComposing = ref(false);

const isDisabled = computed(() => props.disabled || props.loading);

const resolvedPlaceholder = computed(() =>
    props.loading ? props.loadingPlaceholder : props.placeholder,
);

const normalize = (value) =>
    String(value ?? "")
        .trim()
        .toLowerCase();

const getValue = (item) => String(item?.[props.valueKey] ?? "");
const getLabel = (item) => String(item?.[props.labelKey] ?? "");

const filteredItems = computed(() => {
    const trimmedQuery = normalize(query.value);

    if (!trimmedQuery) {
        return props.items.slice(0, props.maxResults);
    }

    return props.items
        .filter((item) => normalize(getLabel(item)).includes(trimmedQuery))
        .slice(0, props.maxResults);
});

const syncQueryWithSelection = () => {
    const selected = props.items.find(
        (item) => getValue(item) === String(props.modelValue ?? ""),
    );

    query.value = selected ? getLabel(selected) : "";
};

const openDropdown = () => {
    if (isDisabled.value) {
        return;
    }

    isDropdownOpen.value = true;
    highlightedIndex.value = filteredItems.value.length ? 0 : -1;
};

const closeDropdown = () => {
    isDropdownOpen.value = false;
    highlightedIndex.value = -1;
};

const onInput = () => {
    if (!isComposing.value) {
        openDropdown();
    }
};

const onKeyup = () => {
    if (!isComposing.value) {
        openDropdown();
    }
};

const onCompositionStart = () => {
    isComposing.value = true;
};

const onCompositionEnd = () => {
    isComposing.value = false;
    openDropdown();
};

const selectItem = (item) => {
    query.value = getLabel(item);
    emit("update:modelValue", getValue(item));
    emit("change", item);
    closeDropdown();
};

const moveHighlight = (step) => {
    if (!isDropdownOpen.value) {
        openDropdown();
        return;
    }

    if (!filteredItems.value.length) {
        highlightedIndex.value = -1;
        return;
    }

    const lastIndex = filteredItems.value.length - 1;

    if (highlightedIndex.value < 0) {
        highlightedIndex.value = step > 0 ? 0 : lastIndex;
        return;
    }

    highlightedIndex.value = Math.max(
        0,
        Math.min(lastIndex, highlightedIndex.value + step),
    );
};

const selectHighlighted = () => {
    if (!isDropdownOpen.value) {
        openDropdown();
        return;
    }

    const item = filteredItems.value[highlightedIndex.value];

    if (item) {
        selectItem(item);
        return;
    }

    syncQueryWithSelection();
    closeDropdown();
};

const onDocumentClick = (event) => {
    const target = event.target;

    if (!root.value || root.value.contains(target)) {
        return;
    }

    syncQueryWithSelection();
    closeDropdown();
};

watch(
    [() => props.modelValue, () => props.items],
    () => {
        syncQueryWithSelection();
    },
    { immediate: true },
);

onMounted(() => {
    document.addEventListener("mousedown", onDocumentClick);
});

onBeforeUnmount(() => {
    document.removeEventListener("mousedown", onDocumentClick);
});
</script>

<style scoped>
.searchable_combobox {
    position: relative;
}

.searchable_combobox__input {
    width: 100%;
}

.searchable_combobox__list {
    position: absolute;
    z-index: 30;
    top: calc(100% + 6px);
    left: 0;
    right: 0;
    max-height: 260px;
    overflow-y: auto;
    margin: 0;
    padding: 6px 0;
    list-style: none;
    background: #fff;
    border: 1px solid #d8d8d8;
    border-radius: 10px;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
}

.searchable_combobox__item,
.searchable_combobox__empty {
    padding: 10px 12px;
    line-height: 1.25;
}

.searchable_combobox__item {
    cursor: pointer;
}

.searchable_combobox__item.active {
    background: #f1f6f4;
}

.searchable_combobox__empty {
    color: #7d7d7d;
}
</style>
