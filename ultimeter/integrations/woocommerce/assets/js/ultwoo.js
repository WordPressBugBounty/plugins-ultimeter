(function($) {
  'use strict';

  $(function() {

    $('#ultimeter_ultwoo_product, #ultimeter_ultwoo_categories').select2({
      width: '50%'
    });
    var dates = $('.ultwoo_datepicker').datepicker({
      changeMonth: true,
      changeYear: true,
      defaultDate: '',
      dateFormat: 'yy-mm-dd',
      numberOfMonths: 1,
      minDate: '-20Y',
      maxDate: '+1D',
      showButtonPanel: true,
      showOn: 'focus',
      buttonImageOnly: true,
      onSelect: function() {
        var option = $(this).is('.from') ? 'minDate' : 'maxDate',
          date = $(this).datepicker('getDate');

        dates.not(this).datepicker('option', option, date);
      }
    });

    function reportMetric() {

      switch ($("._ultimeter_ultwoo_metric_field input:radio:checked").val()) {
        case 'sales_by_date':
          $('._ultimeter_ultwoo_modifier_field').show();
          $('._ultimeter_ultwoo_product_field, ._ultimeter_ultwoo_categories_field, ._ultimeter_ultwoo_custom_unit_field').hide();
          break;
        case 'sales_by_product':
          $('._ultimeter_ultwoo_product_field, ._ultimeter_ultwoo_modifier_field').show();
          $('._ultimeter_ultwoo_categories_field, ._ultimeter_ultwoo_custom_unit_field').hide();
          break;
        case 'units_by_date':
          $('._ultimeter_ultwoo_product_field, ._ultimeter_ultwoo_categories_field, ._ultimeter_ultwoo_modifier_field').hide();
          $('._ultimeter_ultwoo_custom_unit_field').show();
          break;
        case 'units_by_product':
          $('._ultimeter_ultwoo_product_field, ._ultimeter_ultwoo_custom_unit_field').show();
          $('._ultimeter_ultwoo_categories_field, ._ultimeter_ultwoo_modifier_field').hide();
          break;
      }
    }
    reportMetric();

    $('._ultimeter_ultwoo_metric_field').change(function() {
      reportMetric();
    });

    function disableDatePicker() {

      if ($("._ultimeter_ultwoo_time_field input:radio:checked").val() == 'custom') {
        $('.ultwoo_datepicker').prop('disabled', false);
      } else {
        $('.ultwoo_datepicker').prop('disabled', true);
      }
    }
    disableDatePicker();

    $('._ultimeter_ultwoo_time_field').change(function() {
      disableDatePicker();
    });
  });
})(jQuery);