(function ($) {
  "use strict";

  function update_ultimeter() {
    $(".ultimeter-container").each(function () {
      let meter = JSON.parse(
          $(this).attr("data-meter")
      );

      debug(meter);

      animate_meter($(this),meter);
      render_labels($(this),meter);
    });
  }

  function debug(d) {
    console.log(d);
    console.log('Current: ' + get_current(d));
    console.log('Total: ' + get_total(d));
    console.log('Progress: ' + get_progress(d));
    console.log('Formatted Current: ' + get_formatted_current(d));
    console.log('Formatted Total: ' + get_formatted_total(d));
  }

  function animate_meter(e,d) {
    if (d.physics === "height") {
      e.find(".ultimeter_meter_progress").animate({height: get_progress(d) + "%",}, 3000);
    } else {
      e.find(".ultimeter_meter_progress").animate({width: get_progress(d) + "%",}, 3000);
    }
  }

  function render_labels(e,d) {
    e.find(".ultimeter_meter_progress .calculated").text(get_formatted_current(d))
    e.find(".ultimeter_meter_goal .calculated").text(get_formatted_total(d))
  }

  function get_current(meter) {
    return parseFloat(meter.current).toFixed(2);
  }

  function get_total(meter) {
    return parseFloat(meter.total).toFixed(2);
  }

  function get_progress(meter) {
    let current = get_current(meter);
    let total = get_total(meter);

    return parseFloat( ( current * 100 ) / total );
  }

  function get_formatted_current(meter) {
    return number_formatter(meter,get_current(meter));
  }

  function get_formatted_total(meter) {
    return number_formatter(meter,get_total(meter));
  }

  function number_formatter(meter,value) {
    switch (meter.output_type) {
      case 'ultimeter_currency':
      default:
        return new Intl.NumberFormat(meter.language, {
          style: "currency",
          currency: meter.currency,
          minimumFractionDigits: value % 1 === 0 ? 0 : 2,
        }).format(value);
      case 'ultimeter_percentage':
        return new Intl.NumberFormat(meter.language, {
          style: "percent",
        }).format(value / 100);
    }
  }

  $(document).ready(function () {
    update_ultimeter();
  });
})(jQuery);
