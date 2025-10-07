import dayjs from 'dayjs';
import localizedFormat from 'dayjs/plugin/localizedFormat';
import 'dayjs/locale/fr.js';

export default function initDayjs() {
    dayjs.extend(localizedFormat);
    dayjs.locale('fr');
    window.dayjs = dayjs;
    console.log('✅ Day.js initialisé (locale: fr)');
}
