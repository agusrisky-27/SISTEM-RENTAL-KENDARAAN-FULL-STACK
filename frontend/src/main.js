import { createApp } from 'vue'
import { createPinia } from 'pinia'

import App from './App.vue'
import router from './router'

import './assets/style.css'

/* FontAwesome Setup */
import { library } from '@fortawesome/fontawesome-svg-core'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { 
  faChartPie, 
  faCarSide, 
  faUsers, 
  faFileContract, 
  faRotateLeft, 
  faWallet, 
  faUserShield,
  faSun,
  faMoon,
  faBell,
  faPlus,
  faBars,
  faTimes,
  faSearch,
  faUser,
  faBuildingColumns,
  faMoneyBill,
  faQrcode,
  faCreditCard,
  faEdit,
  faTrash,
  faCheck,
  faExclamationTriangle,
  faInfoCircle
} from '@fortawesome/free-solid-svg-icons'

library.add(
  faChartPie, 
  faCarSide, 
  faUsers, 
  faFileContract, 
  faRotateLeft, 
  faWallet, 
  faUserShield,
  faSun,
  faMoon,
  faBell,
  faPlus,
  faBars,
  faTimes,
  faSearch,
  faUser,
  faBuildingColumns,
  faMoneyBill,
  faQrcode,
  faCreditCard,
  faEdit,
  faTrash,
  faCheck,
  faExclamationTriangle,
  faInfoCircle
)

const app = createApp(App)

app.component('font-awesome-icon', FontAwesomeIcon)

app.use(createPinia())
app.use(router)

app.mount('#app')
