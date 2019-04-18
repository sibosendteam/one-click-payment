import 'vue2-toast/lib/toast.css';
import Toast from 'vue2-toast';

require('./../bootstrap');
window.Vue = require('vue');
Vue.prototype.$http = window.axios;
Vue.config.productionTip = false;

Vue.use(Toast);

import PaymentModal from './PaymentModal';


/* eslint-disable no-new */
new Vue({
    el: '#oneclickpayment',
    data: {
        isShowPay: false,
    },
    watch: {
        isShowPay: function (newVal, oldVal) {
            if (newVal == true) {
                this.$nextTick(() => {
                    document.scrollingElement.scrollTop = document.scrollingElement.scrollHeight;
                });
            }
        }
    },
    components: {PaymentModal}
});
