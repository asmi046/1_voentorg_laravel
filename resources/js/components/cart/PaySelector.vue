<template>
<div class="pay_select">
    <div @click.prevent="chengeType(1)" class="pay_typr yoo_cassa" :class="{active:(type == 1)}" >
        Оплата картой онлайн
    </div>

    <div @click.prevent="chengeType(2)" class="pay_typr sbol" :class="{active:(type == 2)}">
        Перевод на карту
    </div>
</div>
</template>

<script>
import {ref} from 'vue'
export default {
    props: {
      modelValue: Number
    },

    emits: ['update:modelValue'],

    setup(props, {emit}) {


        let type = ref(1)

        const chengeType = (value) => {
            console.log(value)
            type.value = value
            emit('update:modelValue', type.value)
        }

        return {
            chengeType,
            type
        }
    }

}
</script>

<style>
    .pay_select {
        width: 100%;
        display: flex;
        justify-content: space-between;
        margin: 10px 0;
        font-size: 12px;
    }

    .pay_typr::before {
        content:"";
        width:25px;
        height:25px;
        background-position: center;
        background-repeat: no-repeat;
        background-size: contain;
        left:5px;
        top: calc(50% - 12px);
    }

    .pay_typr.active {
        background-color: #2A7D5A40;

    }

    .pay_typr {
        width:47%;
        min-height: 40px;
        border-radius: 9px;
        border: 1px solid #2A7D5A;
        padding: 5px 5px 5px 40px;
        position:relative;
        cursor:pointer
    }


</style>
