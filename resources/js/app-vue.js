import "./bootstrap";
import "../css/app.css";
import "@fontsource/poppins/400.css";
import "@fontsource/poppins/500.css";
import "@fontsource/poppins/600.css";
import "@fontsource/poppins/700.css";

import { createApp, h } from "vue";
import { createInertiaApp } from "@inertiajs/vue3";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";
import { ZiggyVue } from "../../vendor/tightenco/ziggy";
import VueTailwindDatepicker from "vue-tailwind-datepicker";
import { library, config } from "@fortawesome/fontawesome-svg-core";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { faSearchengin } from "@fortawesome/free-brands-svg-icons";
import {
    faAngleLeft,
    faAngleUp,
    faAngleDown,
    faArrowsToDot,
    faArrowTrendUp,
    faArrowTrendDown,
    faBan,
    faBars,
    faBarsStaggered,
    faMoneyBillTrendUp,
    faBook,
    faBookBookmark,
    faBuilding,
    faBuildingCircleCheck,
    faCheck,
    faChartPie,
    faChartLine,
    faCheckDouble,
    faChevronLeft,
    faChevronUp,
    faChevronDown,
    faCircle,
    faCircleChevronLeft,
    faCircleDown,
    faCircleXmark,
    faComputer,
    faCoins,
    faCopy,
    faDiagramSuccessor,
    faDiceD6,
    faDisplay,
    faEye,
    faEyeSlash,
    faFile,
    faFileCircleQuestion,
    faFlagCheckered,
    faGraduationCap,
    faHome,
    faIndustry,
    faHourglassHalf,
    faKey,
    faList,
    faListOl,
    faLock,
    faMagnifyingGlass,
    faMagnifyingGlassChart,
    faMeteor,
    faMicrochip,
    faPaperPlane,
    faPercent,
    faPen,
    faPenNib,
    faPencil,
    faPlus,
    faPlusCircle,
    faRecycle,
    faRightToBracket,
    faRotateLeft,
    faRotateRight,
    faSave,
    faScrewdriverWrench,
    faServer,
    faSignOutAlt,
    faSort,
    faSquarePen,
    faStar,
    faSunPlantWilt,
    faTable,
    faTachometerAlt,
    faTags,
    faThList,
    faTrashCan,
    faUser,
    faUserTie,
    faUsers,
    faXmark
} from "@fortawesome/free-solid-svg-icons";
library.add(
    faAngleLeft,
    faAngleUp,
    faAngleDown,
    faArrowsToDot,
    faArrowTrendUp,
    faArrowTrendDown,
    faBan,
    faBars,
    faBarsStaggered,
    faMoneyBillTrendUp,
    faBook,
    faBookBookmark,
    faBuilding,
    faBuildingCircleCheck,
    faCheck,
    faChartPie,
    faChartLine,
    faCheckDouble,
    faChevronLeft,
    faChevronUp,
    faChevronDown,
    faCircle,
    faCircleChevronLeft,
    faCircleDown,
    faCircleXmark,
    faComputer,
    faCoins,
    faCopy,
    faDiagramSuccessor,
    faDiceD6,
    faDisplay,
    faEye,
    faEyeSlash,
    faFile,
    faFileCircleQuestion,
    faFlagCheckered,
    faGraduationCap,
    faHome,
    faIndustry,
    faHourglassHalf,
    faKey,
    faList,
    faListOl,
    faLock,
    faMagnifyingGlass,
    faMagnifyingGlassChart,
    faMeteor,
    faMicrochip,
    faPaperPlane,
    faPercent,
    faPen,
    faPenNib,
    faPencil,
    faPlus,
    faPlusCircle,
    faRecycle,
    faRightToBracket,
    faRotateLeft,
    faRotateRight,
    faSave,
    faScrewdriverWrench,
    faServer,
    faSignOutAlt,
    faSort,
    faSquarePen,
    faStar,
    faSunPlantWilt,
    faSearchengin,
    faTable,
    faTachometerAlt,
    faTags,
    faThList,
    faTrashCan,
    faUser,
    faUserTie,
    faUsers,
    faXmark
);
config.autoAddCss = false;

// const appName = import.meta.env.VITE_APP_NAME || "Laravel";
// const appName = 'MeetSulut';

const setNameApp = () => {
    const currentRoute = route().current()
    const prefix = currentRoute.split('.')[0]
    let result = 'superapp'
    switch (prefix) {
        case 'man-management':
            result = 'ManMent'
            break;
        case 'meeting':
            result = 'MeetSulut'
            break;
        default:
            result = 'superapp'
            break;
    }
    return result;
}
const colornProgress = () => {
    const currentRoute = route().current()
    const prefix = currentRoute.split('.')[0]
    let result = "#4B5563"
    switch (prefix) {
        case 'man-management':
            result = "#8B1E3F"
            break;
        case 'meeting':
            result = "#4B5563"
            break;
        default:
            result = "#4B5563"
            break;
    }
    return result;
}
createInertiaApp({
    title: (title) => `${title} - ${setNameApp()}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob("./Pages/**/*.vue")
        ),
    setup({ el, App, props, plugin }) {
        const vueApp = createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .use(VueTailwindDatepicker)
            .component("font-awesome-icon", FontAwesomeIcon)
        return vueApp.mount(el)
    },
    progress: {
        color: colornProgress(),
        showSpinner: true,
        delay: 250,
    },
});
