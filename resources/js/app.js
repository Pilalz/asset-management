import './bootstrap';

import jQuery from 'jquery';
window.$ = window.jQuery = jQuery;

import 'datatables.net-dt';
import 'flowbite';

import SignaturePad from 'signature_pad';
window.SignaturePad = SignaturePad;

import Alpine from 'alpinejs';

import ApexCharts from 'apexcharts';
window.ApexCharts = ApexCharts;

window.Alpine = Alpine;
Alpine.start();