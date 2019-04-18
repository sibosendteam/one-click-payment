import 'vue2-toast/lib/toast.css';
import Toast from 'vue2-toast';
import VModal from 'vue-js-modal';

require('./../bootstrap');
window.Vue = require('vue');
Vue.prototype.$http = window.axios;
Vue.config.productionTip = false;

Vue.use(Toast);
Vue.use(VModal);


import PaymentModal from './PaymentModal';

/* eslint-disable no-new */
new Vue({
    el: '#oneclickpayment',
    methods: {
        showPayModal: function () {
            this.$modal.show('payment-modal');
        },
    },
    components: {PaymentModal}
})
