import './bootstrap';

import { validateName, checkEmpty, validateCountry, validatePhone, getUrlParam } from './custom';
import { createApp } from 'vue';

import VueApexCharts from "vue3-apexcharts";

import toastr from 'toastr';
window.toastr = toastr;


toastr.options = {
  timeOut: 4000,
  progressBar: true,
}


window.validateCountry = validateCountry;
window.validateName = validateName;
window.checkEmpty = checkEmpty;
window.validatePhone = validatePhone;

const register_info = createApp({});
import register from './components/register_info.vue';
register_info.component('register-info', register);
register_info.mount('#register_info');

const pos = createApp({});
import poselem from './components/pos.vue';
pos.component('pos', poselem);
pos.mount('#pos-elem');

const otherRepair = createApp({});
import otherRepairElem from './components/other_repairs.vue';
otherRepair.component('other-repairs', otherRepairElem);
otherRepair.mount('#other-repairs');

const verviewChartApp = createApp({});
verviewChartApp.use(VueApexCharts);
import overviewchart from './components/overviewchart.vue';
verviewChartApp.component('dashboard-overview', overviewchart);
verviewChartApp.mount('#overviewChart');

const revenewChartApp = createApp({});
revenewChartApp.use(VueApexCharts);
import revenewChart from './components/revenewChart.vue';
revenewChartApp.component('dashboard-revenew', revenewChart);
revenewChartApp.mount('#revenewChart');

const DashboardCharts = createApp({});
DashboardCharts.use(VueApexCharts);
import dashboarchart from './components/dashboardCharts.vue';
DashboardCharts.component('dashboard-charts', dashboarchart);
DashboardCharts.mount('#dashboard_charts');

const DashboardSalesApp = createApp({});
DashboardSalesApp.use(VueApexCharts);
import dashboardSales from './components/sales.vue';
DashboardSalesApp.component('dashboard-sales', dashboardSales);
DashboardSalesApp.mount('#dashboardSales');

const CustomerSalesApp = createApp({});
CustomerSalesApp.use(VueApexCharts);
import customerSales from './components/customerSales.vue';
CustomerSalesApp.component('customer-sales', customerSales);
CustomerSalesApp.mount('#customerSales');

const DashboardCreditApp = createApp({});
import dashboardCredits from './components/credits.vue';
DashboardCreditApp.component('dashboard-credits', dashboardCredits);
DashboardCreditApp.mount('#dashboardCredits');

const DashboardSMS = createApp({});
import dashboardSMS from './components/sendSMS.vue';
DashboardSMS.component('send-sms', dashboardSMS);
DashboardSMS.mount('#dashboardSMS');

const SpareReportComp = createApp({});
import SpareReport from './components/spare_report.vue';
SpareReportComp.component('spare-report', SpareReport);
SpareReportComp.mount('#SpareReport');

const ChinaOrders = createApp({});
import ChinaOrderComp from './components/china-orders.vue';
ChinaOrders.component('china-orders', ChinaOrderComp);
ChinaOrders.mount('#ChinaOrders');

const purchaseOrder = createApp({});
import purchaseForm from './components/purchaseOrder.vue';
purchaseOrder.component('purchase-form', purchaseForm);
purchaseOrder.mount('#purchaseForm');


$('#menu_close').click(function (e) {
  $('.open_menu').removeClass('hide')
  $('.menulist').removeClass('open')
  $(this).addClass('hide');
});

$('.open_menu').click(function (e) {
  $('.menulist').addClass('open')
  $('#menu_close').removeClass('hide');
  $(this).addClass('hide')
});

$("#passwordForm").submit(function (e) {
  e.preventDefault();

  if ($("#newpass").val() == $("#confirmpass").val()) {
    $.ajax({
      type: "post",
      url: "/update-password",
      data: $("#passwordForm").serialize(),
      dataType: "json",
      success: function (response) {
        if (response.error == 0) {
          toastr.success(response.msg, "Success");
          $("#newpass").val("");
          $("#confirmpass").val("");
          $("#oldpass").val("");
        } else {
          toastr.error(response.msg, "Error");
        }
      }
    });
  }
  else {
    toastr.error("Passwords doesn't match", "Error");
  }
});

$(function () {

  $(".progress").each(function () {

    var value = $(this).attr('data-value');
    var left = $(this).find('.progress-left .progress-bar');
    var right = $(this).find('.progress-right .progress-bar');

    if (value > 0) {
      if (value <= 50) {
        right.css('transform', 'rotate(' + percentageToDegrees(value) + 'deg)')
      } else {
        right.css('transform', 'rotate(180deg)')
        left.css('transform', 'rotate(' + percentageToDegrees(value - 50) + 'deg)')
      }
    }

  })

  function percentageToDegrees(percentage) {

    return percentage / 100 * 360

  }

});

$(document).ready(function () {
  $('.select2-multiple').select2();
  $(".data-table").DataTable();
});

$('.no-collapsable').on('click', function (e) {
  e.stopPropagation();
});


var keybuffer = [];
var timeHandler = 1;
function press(event) {

  if (event.which === 13) {
    $("#BarCodeValue").val(keybuffer.join(""));
    $('[aria-controls="DataTables_Table_0"]').val(keybuffer.join(""));
    keybuffer.length = 0;
    return true;
  }

  var number = event.which - 48;
  if (number < 0 || number > 9) {
    return;
  }

  if (timeHandler) {
    clearTimeout(timeHandler)
    keybuffer.push(number);
  }

  timeHandler = setTimeout(function () {
    if (keybuffer.length <= 3) {
      keybuffer.length = 0;
      return
    }
  }, 50);
};

$(document).on("keypress", press);


window.checkFileExtension = function (fileID) {
  var fileName = document.querySelector('#' + fileID).value;
  var extension = fileName.split('.').pop();
  return extension;
};
