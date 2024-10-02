(function ($) {
  "use strict";

  $(function () {
    $(document).on(
      "click",
      "#ultimeter-enterprise-settings #submit ",
      function (e) {
        e.preventDefault();
        let form = $(this).parents("#ultimeter-enterprise-settings");
        save_enterprise_settings(form);
      }
    );
  });

  function save_enterprise_settings(form) {
    console.log(form);

    let nonce = form.data("nonce");
    $.ajax({
      type: "POST",
      dataType: "JSON",
      url: ultimeter.ajaxurl,
      data: {
        data: form.serialize(),
        nonce: nonce,
        action: "save_enterprise_settings__premium_only",
      },
      success: function (response) {
        if (!response.success) {
          console.log("Ultimeter Error Output: " + response.data[0].message);
        } else {
          form.find("#settings-result").show().delay(2000).fadeOut();
        }
      },
      error: function (xhr, textStatus, errorThrown) {
        console.log(textStatus);
        console.log(errorThrown);
      },
    });
  }

  $(function () {
    function showhide(e) {
      if (e == "progressbar") {
        $(".csf-section.visual-options .progressbar").show();
      } else {
        $(".csf-section.visual-options .progressbar").hide();
      }

      if (e == "radial") {
        $(".csf-section.visual-options .radial").show();
      } else {
        $(".csf-section.visual-options .radial").hide();
      }

      if (e == "infinite") {
        $(".csf-section.visual-options .infinite").show();
      } else {
        $(".csf-section.visual-options .infinite").hide();
      }

      if (e == "thermometer" || e == "verticalprogress") {
        $(".csf-section.visual-options .thermometer").show();
      } else {
        $(".csf-section.visual-options .thermometer").hide();
      }

      if (e == "radial" || e == "custom" || e == "infinite") {
        $(".csf-nav .fa-calendar-check").closest("li").hide();
      } else {
        $(".csf-nav .fa-calendar-check").closest("li").show();
      }

      if ("true" == ultimeter.can_use_premium_code) {
        if (e == "custom") {
          $(".csf-nav .fa-pencil-ruler").closest("li").show();
        } else {
          $(".csf-nav .fa-pencil-ruler").closest("li").hide();
        }
      } else {
        $(".csf-nav .fa-pencil-ruler").closest("li").show();
      }
    }

    var initial = $(".csf-section.meter-type input:checked").val();
    showhide(initial);

    $(".csf-section.meter-type input").change(function () {
      showhide(this.value);
    });
  });

  $(function () {
    $(
      ".csf-field-connected_image_select,.csf-field-image_select_style_packs"
    ).each(function () {
      var $this = $(this),
        $titles = $this.find(".csf-connected-image-select-group-title"),
        $siblings = $this.find(".csf--sibling"),
        multiple = $this.data("multiple") || false;

      $titles.on("click", function () {
        var $title = $(this),
          $icon = $title.find(".csf-set-icon"),
          $content = $title.next();

        if ($icon.hasClass("fa-angle-right")) {
          $icon.removeClass("fa-angle-right").addClass("fa-angle-down");
        } else {
          $icon.removeClass("fa-angle-down").addClass("fa-angle-right");
        }

        if (!$content.data("opened")) {
          // $content.csf_reload_script();
          $content.data("opened", true);
        }

        $content.toggleClass("csf-connected-image-select-group-content-open");
      });

      $siblings.on("click", function () {
        var $sibling = $(this);

        if (multiple) {
          if ($sibling.hasClass("csf--active")) {
            $sibling.removeClass("csf--active");
            $sibling.find("input").prop("checked", false).trigger("change");
          } else {
            $sibling.addClass("csf--active");
            $sibling.find("input").prop("checked", true).trigger("change");
          }
        } else {
          $this.find("input").prop("checked", false);
          $sibling.find("input").prop("checked", true).trigger("change");
          $siblings.removeClass("csf--active");
          $sibling.addClass("csf--active");
        }
      });
    });
  });

  $(function () {
    $(".ultimeter-rating-link").on("click", function () {
      $(this).parent().text($(this).data("rated"));
    });
  });

  // $(function () {
  //   $("._ultimeter_force_centering_field").hide();
  //
  //   if (
  //     "thermometer" ===
  //     $("._ultimeter_meter_type_field input:radio:checked").val()
  //   ) {
  //     $("._ultimeter_force_centering_field").show();
  //   } else {
  //     $("._ultimeter_force_centering_field").hide();
  //   }
  //
  //   $("input[type=radio][name=_ultimeter_meter_type]").change(function () {
  //     if (this.value == "thermometer") {
  //       $("._ultimeter_force_centering_field").show();
  //     } else {
  //       $("._ultimeter_force_centering_field").hide();
  //     }
  //   });
  // });

  // $(function () {
  //   $("._ultimeter_meter_size_field").hide();
  //
  //   if (
  //     "thermometer2020" ===
  //     $("._ultimeter_meter_type_field input:radio:checked").val()
  //   ) {
  //     $("._ultimeter_meter_size_field").show();
  //   } else {
  //     $("._ultimeter_meter_size_field").hide();
  //   }
  //
  //   $("input[type=radio][name=_ultimeter_meter_type]").change(function () {
  //     if (this.value == "thermometer2020") {
  //       $("._ultimeter_meter_size_field").show();
  //     } else {
  //       $("._ultimeter_meter_size_field").hide();
  //     }
  //   });
  // });

  // $(function () {
  //   $(".form_celebrations_tab").hide();
  //
  //   switch ($("._ultimeter_meter_type_field input:radio:checked").val()) {
  //     case "thermometer2020":
  //     case "radial":
  //     case "progressbar":
  //     case "basictrack":
  //     case "scalable":
  //       $(".form_celebrations_tab").show();
  //       break;
  //     default:
  //       $(".form_celebrations_tab").hide();
  //       break;
  //   }
  //
  //   $("input[type=radio][name=_ultimeter_meter_type]").change(function () {
  //     switch (this.value) {
  //       case "thermometer2020":
  //       case "radial":
  //       case "progressbar":
  //       case "basictrack":
  //       case "scalable":
  //         $(".form_celebrations_tab").show();
  //         break;
  //       default:
  //         $(".form_celebrations_tab").hide();
  //         break;
  //     }
  //   });
  // });

  // $(function () {
  //   $(".form_custom_meter_tab").hide();
  //   $(".form_infinite_meter_tab").hide();
  //   $(".form_progressbar_tab").hide();
  //
  //   if (
  //     "custom" === $("._ultimeter_meter_type_field input:radio:checked").val()
  //   ) {
  //     $(".form_custom_meter_tab").show();
  //     $(".form_infinite_meter_tab").hide();
  //     $(".form_milestones_tab").hide();
  //     $(".form_progressbar_tab").hide();
  //   } else if (
  //     "infinite" === $("._ultimeter_meter_type_field input:radio:checked").val()
  //   ) {
  //     $(".form_infinite_meter_tab").show();
  //     $(".form_custom_meter_tab").hide();
  //     $(".form_milestones_tab").hide();
  //     $(".form_progressbar_tab").hide();
  //   } else if (
  //     "radial" === $("._ultimeter_meter_type_field input:radio:checked").val()
  //   ) {
  //     $(".form_infinite_meter_tab").hide();
  //     $(".form_custom_meter_tab").hide();
  //     $(".form_milestones_tab").hide();
  //     $(".form_progressbar_tab").hide();
  //   } else if (
  //     "progressbar" ===
  //     $("._ultimeter_meter_type_field input:radio:checked").val()
  //   ) {
  //     $(".form_infinite_meter_tab").hide();
  //     $(".form_custom_meter_tab").hide();
  //     $(".form_milestones_tab").show();
  //     $(".form_progressbar_tab").show();
  //   } else {
  //     $(".form_custom_meter_tab").hide();
  //     $(".form_infinite_meter_tab").hide();
  //     $(".form_milestones_tab").show();
  //     $(".form_progressbar_tab").hide();
  //   }
  //
  //   $("input[type=radio][name=_ultimeter_meter_type]").change(function () {
  //     if (this.value == "custom") {
  //       $(".form_custom_meter_tab").show();
  //       $(".form_infinite_meter_tab").hide();
  //       $(".form_milestones_tab").hide();
  //       $(".form_progressbar_tab").hide();
  //     } else if (this.value == "infinite") {
  //       $(".form_infinite_meter_tab").show();
  //       $(".form_custom_meter_tab").hide();
  //       $(".form_milestones_tab").hide();
  //       $(".form_progressbar_tab").hide();
  //     } else if (this.value == "radial") {
  //       $(".form_infinite_meter_tab").hide();
  //       $(".form_custom_meter_tab").hide();
  //       $(".form_milestones_tab").hide();
  //       $(".form_progressbar_tab").hide();
  //     } else if (this.value == "progressbar") {
  //       $(".form_infinite_meter_tab").hide();
  //       $(".form_custom_meter_tab").hide();
  //       $(".form_milestones_tab").show();
  //       $(".form_progressbar_tab").show();
  //     } else {
  //       $(".form_custom_meter_tab").hide();
  //       $(".form_infinite_meter_tab").hide();
  //       $(".form_milestones_tab").show();
  //       $(".form_progressbar_tab").hide();
  //     }
  //   });
  // });

  // $(function () {
  //   $("._ultimeter_infinite_meter_duration_field").hide();
  //   $("._ultimeter_infinite_meter_progress_size_field").hide();
  //   $("._ultimeter_infinite_meter_goal_size_field").hide();
  //   $("._ultimeter_infinite_meter_toggle_goal_field").hide();
  //   $("._ultimeter_infinite_meter_tick_number_format_field").hide();
  //   $("._ultimeter_infinite_meter_tick_symbol_placement_field").hide();
  //
  //   if (
  //     "inline" ===
  //     $("._ultimeter_infinite_meter_type_field input:radio:checked").val()
  //   ) {
  //     $("._ultimeter_infinite_meter_duration_field").show();
  //     $("._ultimeter_infinite_meter_progress_size_field").hide();
  //     $("._ultimeter_infinite_meter_goal_size_field").hide();
  //     $("._ultimeter_infinite_meter_toggle_goal_field").hide();
  //     $("._ultimeter_infinite_meter_tick_number_format_field").hide();
  //     $("._ultimeter_infinite_meter_tick_symbol_placement_field").hide();
  //   } else if (
  //     "basic" ===
  //     $("._ultimeter_infinite_meter_type_field input:radio:checked").val()
  //   ) {
  //     $("._ultimeter_infinite_meter_duration_field").show();
  //     $("._ultimeter_infinite_meter_progress_size_field").show();
  //     $("._ultimeter_infinite_meter_goal_size_field").show();
  //     $("._ultimeter_infinite_meter_toggle_goal_field").show();
  //     $("._ultimeter_infinite_meter_tick_number_format_field").hide();
  //     $("._ultimeter_infinite_meter_tick_symbol_placement_field").hide();
  //   } else if (
  //     "flip" ===
  //     $("._ultimeter_infinite_meter_type_field input:radio:checked").val()
  //   ) {
  //     $("._ultimeter_infinite_meter_duration_field").show();
  //     $("._ultimeter_infinite_meter_progress_size_field").hide();
  //     $("._ultimeter_infinite_meter_goal_size_field").hide();
  //     $("._ultimeter_infinite_meter_toggle_goal_field").hide();
  //     $("._ultimeter_infinite_meter_tick_number_format_field").show();
  //     $("._ultimeter_infinite_meter_tick_symbol_placement_field").show();
  //   }
  //
  //   $("input[type=radio][name=_ultimeter_infinite_meter_type]").change(
  //     function () {
  //       if (this.value == "inline") {
  //         $("._ultimeter_infinite_meter_duration_field").show();
  //         $("._ultimeter_infinite_meter_progress_size_field").hide();
  //         $("._ultimeter_infinite_meter_goal_size_field").hide();
  //         $("._ultimeter_infinite_meter_toggle_goal_field").hide();
  //         $("._ultimeter_infinite_meter_tick_number_format_field").hide();
  //         $("._ultimeter_infinite_meter_tick_symbol_placement_field").hide();
  //       } else if (this.value == "basic") {
  //         $("._ultimeter_infinite_meter_duration_field").show();
  //         $("._ultimeter_infinite_meter_progress_size_field").show();
  //         $("._ultimeter_infinite_meter_goal_size_field").show();
  //         $("._ultimeter_infinite_meter_toggle_goal_field").show();
  //         $("._ultimeter_infinite_meter_tick_number_format_field").hide();
  //         $("._ultimeter_infinite_meter_tick_symbol_placement_field").hide();
  //       } else if (this.value == "flip") {
  //         $("._ultimeter_infinite_meter_duration_field").show();
  //         $("._ultimeter_infinite_meter_progress_size_field").hide();
  //         $("._ultimeter_infinite_meter_goal_size_field").hide();
  //         $("._ultimeter_infinite_meter_toggle_goal_field").hide();
  //         $("._ultimeter_infinite_meter_tick_number_format_field").show();
  //         $("._ultimeter_infinite_meter_tick_symbol_placement_field").show();
  //       }
  //     }
  //   );
  // });

  // $(function () {
  //   $(".form_givewp_tab").hide();
  //
  //   "givewp" === $("._ultimeter_goal_format_field input:radio:checked").val()
  //     ? $(".form_givewp_tab").show()
  //     : $(".form_givewp_tab").hide();
  //   $("input[type=radio][name=_ultimeter_goal_format]").change(function () {
  //     if (this.value == "givewp") {
  //       $(".form_givewp_tab").show();
  //     } else {
  //       $(".form_givewp_tab").hide();
  //     }
  //   });
  // });

  // $(function () {
  //   $(".form_charitable_tab").hide();
  //
  //   "charitable" ===
  //   $("._ultimeter_goal_format_field input:radio:checked").val()
  //     ? $(".form_charitable_tab").show()
  //     : $(".form_charitable_tab").hide();
  //   $("input[type=radio][name=_ultimeter_goal_format]").change(function () {
  //     if (this.value == "charitable") {
  //       $(".form_charitable_tab").show();
  //     } else {
  //       $(".form_charitable_tab").hide();
  //     }
  //   });
  // });

  // $(function () {
  //   $(".form_gravity_tab").hide();
  //
  //   "gravity" === $("._ultimeter_goal_format_field input:radio:checked").val()
  //     ? $(".form_gravity_tab").show()
  //     : $(".form_gravity_tab").hide();
  //   $("input[type=radio][name=_ultimeter_goal_format]").change(function () {
  //     if (this.value == "gravity") {
  //       $(".form_gravity_tab").show();
  //     } else {
  //       $(".form_gravity_tab").hide();
  //     }
  //   });
  // });

  // $(function () {
  //   $("#ultimeter_infinite_meter_duration_slider").slider({
  //     value: $("#ultimeter_infinite_meter_duration").val(),
  //     min: 0,
  //     max: 7000,
  //     step: 500,
  //     slide: function (event, ui) {
  //       console.log(ui.value);
  //       $("#ultimeter_infinite_meter_duration_visual").val(ui.value);
  //       $("#ultimeter_infinite_meter_duration").val(ui.value);
  //       console.log($("#ultimeter_infinite_meter_duration_visual").val());
  //       $("#ultimeter_infinite_meter_duration_visual").attr("value", ui.value);
  //     },
  //   });
  //   $("#ultimeter_infinite_meter_duration").val(
  //     $("#ultimeter_infinite_meter_duration_slider").slider("value")
  //   );
  // });

  // $(function () {
  //   $("#_ultimeter_goal_custom").on("change paste keyup", function () {
  //     console.log("changed");
  //     if ($("#_ultimeter_goal_custom").val() == 1) {
  //       $("#ultimeter-custom-unit-span-goal").text(
  //         $("#_ultimeter_singular_custom").val()
  //       );
  //     } else {
  //       $("#ultimeter-custom-unit-span-goal").text(
  //         $("#_ultimeter_plural_custom").val()
  //       );
  //     }
  //   });
  //
  //   $("#_ultimeter_raised_custom").on("change paste keyup", function () {
  //     console.log("changed");
  //     if ($("#_ultimeter_raised_custom").val() == 1) {
  //       $(".ultimeter-custom-unit-span").text(
  //         $("#_ultimeter_singular_custom").val()
  //       );
  //     } else {
  //       $(".ultimeter-custom-unit-span").text(
  //         $("#_ultimeter_plural_custom").val()
  //       );
  //     }
  //   });
  //
  //   $("#_ultimeter_plural_custom, #_ultimeter_singular_custom").on(
  //     "change paste keyup",
  //     function () {
  //       if ($("#_ultimeter_raised_custom").val() == 1) {
  //         $(".ultimeter-custom-unit-span").text(
  //           $("#_ultimeter_singular_custom").val()
  //         );
  //       } else {
  //         $(".ultimeter-custom-unit-span").text(
  //           $("#_ultimeter_plural_custom").val()
  //         );
  //       }
  //
  //       if ($("#_ultimeter_goal_custom").val() == 1) {
  //         $(".ultimeter-custom-unit-span").text(
  //           $("#_ultimeter_singular_custom").val()
  //         );
  //       } else {
  //         $(".ultimeter-custom-unit-span").text(
  //           $("#_ultimeter_plural_custom").val()
  //         );
  //       }
  //     }
  //   );
  // });

  // $(function () {
  //   function updateCurrency() {
  //     if ($("#_ultimeter_currency").length) {
  //       var a = $("#_ultimeter_currency :selected").val();
  //       var b = new Intl.NumberFormat("en-US", {
  //         style: "currency",
  //         currency: a,
  //       }).format(0);
  //       var c = b.replace(/\d+([,.]\d+)?/g, "");
  //       $(".ultimeter-money-symbol").text(c);
  //     }
  //   }
  //   updateCurrency();
  //
  //   $("#_ultimeter_currency").change(function () {
  //     updateCurrency();
  //   });
  //
  //   function toggleMilestones() {
  //     switch (
  //       $("._ultimeter_milestones_toggle_field input:radio:checked").val()
  //     ) {
  //       case "yes":
  //         $("#_ultimeter_milestones_field").show();
  //         break;
  //       case "no":
  //         $("#_ultimeter_milestones_field").hide();
  //         break;
  //     }
  //   }
  //   toggleMilestones();
  //
  //   $("._ultimeter_milestones_toggle_field").change(function () {
  //     toggleMilestones();
  //   });
  //
  //   function toggleOffsetFields() {
  //     switch (
  //       $("._ultimeter_custom_orientation_field input:radio:checked").val()
  //     ) {
  //       case "bt":
  //         $("._ultimeter_custom_offset_top_field").show();
  //         $("._ultimeter_custom_offset_bottom_field").show();
  //         break;
  //       default:
  //         $("._ultimeter_custom_offset_top_field").hide();
  //         $("._ultimeter_custom_offset_bottom_field").hide();
  //         break;
  //     }
  //   }
  //   toggleOffsetFields();
  //
  //   $("._ultimeter_custom_orientation_field").change(function () {
  //     toggleOffsetFields();
  //   });
  //
  //   function goalFormat() {
  //     switch ($("._ultimeter_goal_format_field input:radio:checked").val()) {
  //       case "amount":
  //         $("._ultimeter_currency_field").show();
  //         $("._ultimeter_goal_amount_field").show();
  //         $("._ultimeter_raised_amount_field").show();
  //         $("._ultimeter_boost_raised_amount_field").show();
  //         $("._ultimeter_raised_percentage_field").hide();
  //         $("._ultimeter_boost_raised_percentage_field").hide();
  //         $("._ultimeter_goal_custom_field").hide();
  //         $("._ultimeter_raised_custom_field").hide();
  //         $("._ultimeter_boost_raised_custom_field").hide();
  //         $("._ultimeter_singular_custom_field").hide();
  //         $("._ultimeter_plural_custom_field").hide();
  //         $("._ultimeter_woocommerce_field").hide();
  //         $("._ultimeter_woo_goal_field").hide();
  //         break;
  //       case "percentage":
  //         $("._ultimeter_currency_field").hide();
  //         $("._ultimeter_goal_amount_field").hide();
  //         $("._ultimeter_raised_amount_field").hide();
  //         $("._ultimeter_boost_raised_amount_field").hide();
  //         $("._ultimeter_raised_percentage_field").show();
  //         $("._ultimeter_boost_raised_percentage_field").show();
  //         $("._ultimeter_goal_custom_field").hide();
  //         $("._ultimeter_raised_custom_field").hide();
  //         $("._ultimeter_boost_raised_custom_field").hide();
  //         $("._ultimeter_singular_custom_field").hide();
  //         $("._ultimeter_plural_custom_field").hide();
  //         $("._ultimeter_woocommerce_field").hide();
  //         $("._ultimeter_woo_goal_field").hide();
  //         break;
  //       case "custom":
  //         $("._ultimeter_currency_field").hide();
  //         $("._ultimeter_goal_amount_field").hide();
  //         $("._ultimeter_raised_amount_field").hide();
  //         $("._ultimeter_boost_raised_amount_field").hide();
  //         $("._ultimeter_raised_percentage_field").hide();
  //         $("._ultimeter_boost_raised_percentage_field").hide();
  //         $("._ultimeter_goal_custom_field").show();
  //         $("._ultimeter_raised_custom_field").show();
  //         $("._ultimeter_boost_raised_custom_field").show();
  //         $("._ultimeter_singular_custom_field").show();
  //         $("._ultimeter_plural_custom_field").show();
  //         $("._ultimeter_woocommerce_field").hide();
  //         $("._ultimeter_woo_goal_field").hide();
  //         break;
  //       case "woo":
  //         $("._ultimeter_currency_field").show();
  //         $("._ultimeter_goal_amount_field").hide();
  //         $("._ultimeter_raised_amount_field").hide();
  //         $("._ultimeter_raised_percentage_field").hide();
  //         $("._ultimeter_goal_custom_field").hide();
  //         $("._ultimeter_raised_custom_field").hide();
  //         $("._ultimeter_singular_custom_field").hide();
  //         $("._ultimeter_plural_custom_field").hide();
  //         $("._ultimeter_woocommerce_field").show();
  //         $("._ultimeter_woo_goal_field").show();
  //         $("._ultimeter_boost_raised_percentage_field").hide();
  //         $("._ultimeter_boost_raised_amount_field").hide();
  //         $("._ultimeter_boost_raised_custom_field").hide();
  //         break;
  //       case "givewp":
  //         $("._ultimeter_currency_field").show();
  //         $("._ultimeter_goal_amount_field").hide();
  //         $("._ultimeter_raised_amount_field").hide();
  //         $("._ultimeter_raised_percentage_field").hide();
  //         $("._ultimeter_goal_custom_field").hide();
  //         $("._ultimeter_raised_custom_field").hide();
  //         $("._ultimeter_singular_custom_field").hide();
  //         $("._ultimeter_plural_custom_field").hide();
  //         $("._ultimeter_woocommerce_field").hide();
  //         $("._ultimeter_woo_goal_field").hide();
  //         $("._ultimeter_boost_raised_percentage_field").hide();
  //         $("._ultimeter_boost_raised_amount_field").hide();
  //         $("._ultimeter_boost_raised_custom_field").hide();
  //         break;
  //       case "charitable":
  //         $("._ultimeter_currency_field").show();
  //         $("._ultimeter_goal_amount_field").hide();
  //         $("._ultimeter_raised_amount_field").hide();
  //         $("._ultimeter_raised_percentage_field").hide();
  //         $("._ultimeter_goal_custom_field").hide();
  //         $("._ultimeter_raised_custom_field").hide();
  //         $("._ultimeter_singular_custom_field").hide();
  //         $("._ultimeter_plural_custom_field").hide();
  //         $("._ultimeter_woocommerce_field").hide();
  //         $("._ultimeter_woo_goal_field").hide();
  //         $("._ultimeter_boost_raised_percentage_field").hide();
  //         $("._ultimeter_boost_raised_amount_field").hide();
  //         $("._ultimeter_boost_raised_custom_field").hide();
  //         break;
  //       case "woocommerce":
  //         $("._ultimeter_currency_field").show();
  //         $("._ultimeter_goal_amount_field").hide();
  //         $("._ultimeter_raised_amount_field").hide();
  //         $("._ultimeter_raised_percentage_field").hide();
  //         $("._ultimeter_goal_custom_field").hide();
  //         $("._ultimeter_raised_custom_field").hide();
  //         $("._ultimeter_singular_custom_field").hide();
  //         $("._ultimeter_plural_custom_field").hide();
  //         $("._ultimeter_woocommerce_field").hide();
  //         $("._ultimeter_woo_goal_field").hide();
  //         $("._ultimeter_boost_raised_percentage_field").hide();
  //         $("._ultimeter_boost_raised_amount_field").hide();
  //         $("._ultimeter_boost_raised_custom_field").hide();
  //         break;
  //       case "gravity":
  //         $("._ultimeter_currency_field").show();
  //         $("._ultimeter_goal_amount_field").hide();
  //         $("._ultimeter_raised_amount_field").hide();
  //         $("._ultimeter_raised_percentage_field").hide();
  //         $("._ultimeter_goal_custom_field").hide();
  //         $("._ultimeter_raised_custom_field").hide();
  //         $("._ultimeter_singular_custom_field").show();
  //         $("._ultimeter_plural_custom_field").show();
  //         $("._ultimeter_woocommerce_field").hide();
  //         $("._ultimeter_woo_goal_field").hide();
  //         $("._ultimeter_boost_raised_percentage_field").hide();
  //         $("._ultimeter_boost_raised_amount_field").hide();
  //         $("._ultimeter_boost_raised_custom_field").hide();
  //         break;
  //       case "ultwooenterprise":
  //         $("._ultimeter_currency_field").show();
  //         $("._ultimeter_goal_amount_field").hide();
  //         $("._ultimeter_raised_amount_field").hide();
  //         $("._ultimeter_raised_percentage_field").hide();
  //         $("._ultimeter_goal_custom_field").hide();
  //         $("._ultimeter_raised_custom_field").hide();
  //         $("._ultimeter_singular_custom_field").hide();
  //         $("._ultimeter_plural_custom_field").hide();
  //         $("._ultimeter_woocommerce_field").hide();
  //         $("._ultimeter_woo_goal_field").hide();
  //         $("._ultimeter_boost_raised_percentage_field").hide();
  //         $("._ultimeter_boost_raised_amount_field").hide();
  //         $("._ultimeter_boost_raised_custom_field").hide();
  //         break;
  //     }
  //   }
  //   goalFormat();
  //
  //   $("._ultimeter_goal_format_field").change(function () {
  //     goalFormat();
  //   });
  //
  //   function givewpFormat() {
  //     switch ($("._ultimeter_givewp_format_field input:radio:checked").val()) {
  //       case "amount":
  //         $("._ultimeter_givewp_amount_field").show();
  //         $(
  //           "._ultimeter_givewp_donations_field, ._ultimeter_givewp_donors_field"
  //         ).hide();
  //         break;
  //       case "percentage":
  //         $("._ultimeter_givewp_amount_field").show();
  //         $(
  //           "._ultimeter_givewp_donations_field, ._ultimeter_givewp_donors_field"
  //         ).hide();
  //         break;
  //       case "donations":
  //         $("._ultimeter_givewp_donations_field").show();
  //         $(
  //           "._ultimeter_givewp_amount_field, ._ultimeter_givewp_donors_field"
  //         ).hide();
  //         break;
  //       case "donors":
  //         $("._ultimeter_givewp_donors_field").show();
  //         $(
  //           "._ultimeter_givewp_amount_field, ._ultimeter_givewp_donations_field"
  //         ).hide();
  //         break;
  //     }
  //   }
  //   givewpFormat();
  //
  //   $("._ultimeter_charitable_format_field").change(function () {
  //     charitableFormat();
  //   });
  //
  //   function charitableFormat() {
  //     switch (
  //       $("._ultimeter_charitable_format_field input:radio:checked").val()
  //     ) {
  //       case "amount":
  //         $("._ultimeter_charitable_amount_field").show();
  //         $(
  //           "._ultimeter_charitable_donations_field, ._ultimeter_charitable_donors_field"
  //         ).hide();
  //         break;
  //       case "percentage":
  //         $("._ultimeter_charitable_amount_field").show();
  //         $(
  //           "._ultimeter_charitable_donations_field, ._ultimeter_charitable_donors_field"
  //         ).hide();
  //         break;
  //       case "donations":
  //         $("._ultimeter_charitable_donations_field").show();
  //         $(
  //           "._ultimeter_charitable_amount_field, ._ultimeter_charitable_donors_field"
  //         ).hide();
  //         break;
  //     }
  //   }
  //   charitableFormat();
  //
  //   $("._ultimeter_charitable_format_field").change(function () {
  //     charitableFormat();
  //   });
  //
  //   $("._ultimeter_gravity_format_field").change(function () {
  //     gravityFormat();
  //   });
  //
  //   function gravityFormat() {
  //     switch ($("._ultimeter_gravity_format_field input:radio:checked").val()) {
  //       case "totals":
  //         $("._ultimeter_gravity_totals_goal_field").show();
  //         $("._ultimeter_gravity_entries_goal_field").hide();
  //         $("._ultimeter_boost_gravity_totals_raised_field").show();
  //         $("._ultimeter_boost_gravity_entries_raised_field").hide();
  //         break;
  //       case "entries":
  //         $("._ultimeter_gravity_entries_goal_field").show();
  //         $("._ultimeter_gravity_totals_goal_field").hide();
  //         $("._ultimeter_boost_gravity_totals_raised_field").hide();
  //         $("._ultimeter_boost_gravity_entries_raised_field").show();
  //         break;
  //     }
  //   }
  //   gravityFormat();
  //
  //   function update_milestones() {}
  //   var m = [];
  //
  //   function addMilestone() {
  //     var n = Math.floor(Math.random() * 10000 + 1);
  //
  //     var template = $(".ultimeter-template").clone(true);
  //
  //     template.removeClass("ultimeter-template");
  //
  //     template.find(".ultimeter-milestone-id-value").html(n);
  //
  //     template.find("*").each(function () {
  //       for (var i = 0; i < this.attributes.length; i++) {
  //         var attrib = this.attributes[i];
  //         attrib.value = attrib.value.replace("{{rand}}", n);
  //       }
  //     });
  //
  //     template.appendTo("#ultimeter-milestones-table");
  //   }
  //   $(".ultimeter-add-repeater-field-section-row").on("click", addMilestone);
  //   // console.log(m);
  //
  //   function removeMilestone() {
  //     this.closest("tr").remove();
  //     // var n = $('#ultimeter-milestones-table').attr('data-rf-row-count');
  //     // n--;
  //     // $('#ultimeter-milestones-table').attr('data-rf-row-count', n);
  //   }
  //   $(".ultimeter-remove").on("click", removeMilestone);
  // });

  // $(function () {
  //   $(".ultimeter-color-picker").wpColorPicker();
  // });

  $(function () {
    var clippy = new ClipboardJS(".ultimeter-shortcode-button");

    clippy.on("success", function () {
      $(".ultimeter-shortcode-button").html(
        '<span class="dashicons dashicons-yes"></span> Copied!'
      );
      setTimeout(function () {
        $(".ultimeter-shortcode-button").html(
          '<span class="dashicons dashicons-admin-page"></span> Copy Shortcode'
        );
      }, 3000);
    });
  });

  // $(function () {
  //   $(".ultimeter-image-thumb").hide();
  //
  //   $("._ultimeter_custom_meter_image_field").each(function () {
  //     var f = $(this).closest("fieldset");
  //     var t = $(f).find(".ultimeter-image-thumb > img");
  //     if ($(this).val()) {
  //       $(f).find(".ultimeter-image-thumb").show();
  //       $(t).attr("src", $(this).val());
  //     }
  //   });
  //
  //   /*
  //    * Select/Upload image(s) event
  //    */
  //   $(".ultimeter-upload-button").on("click", function (e) {
  //     e.preventDefault();
  //     var f = $(this).closest("fieldset");
  //     var i = $(f).find("input[type=text]");
  //     var t = $(f).find(".ultimeter-image-thumb > img");
  //
  //     var button = $(this),
  //       custom_uploader = wp
  //         .media({
  //           title: "Insert image",
  //           library: {
  //             type: "image",
  //           },
  //           button: {
  //             text: "Use this image",
  //           },
  //           multiple: false, // for multiple image selection set to true
  //         })
  //         .on("select", function () {
  //           // it also has "open" and "close" events
  //           var attachment = custom_uploader
  //             .state()
  //             .get("selection")
  //             .first()
  //             .toJSON();
  //           i.val(attachment.url);
  //           t.attr("src", attachment.url);
  //           $(f).find(".ultimeter-image-thumb").show();
  //         })
  //         .open();
  //   });
  //
  //   /*
  //    * Remove image event
  //    */
  //   $(".ultimeter-delete-image-thumb").on("click", function (e) {
  //     e.preventDefault();
  //     var f = $(this).closest("fieldset");
  //     var i = $(f).find("input[type=text]");
  //     var t = $(f).find(".ultimeter-image-thumb > img");
  //     $(f).find(".ultimeter-image-thumb").hide();
  //
  //     i.val("");
  //     t.attr("src", "");
  //   });
  // });

  // $(function () {
  //   // $("#ultimeter_ultwoo_product, #ultimeter_ultwoo_categories").select2({
  //   //   width: "50%",
  //   // });
  //   var dates = $(".ultwoo_datepicker").datepicker({
  //     changeMonth: true,
  //     changeYear: true,
  //     defaultDate: "",
  //     dateFormat: "yy-mm-dd",
  //     numberOfMonths: 1,
  //     minDate: "-20Y",
  //     maxDate: "+1D",
  //     showButtonPanel: true,
  //     showOn: "focus",
  //     buttonImageOnly: true,
  //     onSelect: function () {
  //       var option = $(this).is(".from") ? "minDate" : "maxDate",
  //         date = $(this).datepicker("getDate");
  //
  //       dates.not(this).datepicker("option", option, date);
  //     },
  //   });
  //
  //   function reportMetric() {
  //     switch ($("._ultimeter_ultwoo_metric_field input:radio:checked").val()) {
  //       case "sales_by_date":
  //         $("._ultimeter_ultwoo_modifier_field").show();
  //         $(
  //           "._ultimeter_ultwoo_product_field, ._ultimeter_ultwoo_categories_field, ._ultimeter_ultwoo_custom_unit_field"
  //         ).hide();
  //         break;
  //       case "sales_by_product":
  //         $(
  //           "._ultimeter_ultwoo_product_field, ._ultimeter_ultwoo_modifier_field"
  //         ).show();
  //         $(
  //           "._ultimeter_ultwoo_categories_field, ._ultimeter_ultwoo_custom_unit_field"
  //         ).hide();
  //         break;
  //       case "units_by_date":
  //         $(
  //           "._ultimeter_ultwoo_product_field, ._ultimeter_ultwoo_categories_field, ._ultimeter_ultwoo_modifier_field"
  //         ).hide();
  //         $("._ultimeter_ultwoo_custom_unit_field").show();
  //         break;
  //       case "units_by_product":
  //         $(
  //           "._ultimeter_ultwoo_product_field, ._ultimeter_ultwoo_custom_unit_field"
  //         ).show();
  //         $(
  //           "._ultimeter_ultwoo_categories_field, ._ultimeter_ultwoo_modifier_field"
  //         ).hide();
  //         break;
  //     }
  //   }
  //   reportMetric();
  //
  //   $("._ultimeter_ultwoo_metric_field").change(function () {
  //     reportMetric();
  //   });
  //
  //   function disableDatePicker() {
  //     if (
  //       $("._ultimeter_ultwoo_time_field input:radio:checked").val() == "custom"
  //     ) {
  //       $(".ultwoo_datepicker").prop("disabled", false);
  //     } else {
  //       $(".ultwoo_datepicker").prop("disabled", true);
  //     }
  //   }
  //   disableDatePicker();
  //
  //   $("._ultimeter_ultwoo_time_field").change(function () {
  //     disableDatePicker();
  //   });
  // });
})(jQuery);

/*!
 * clipboard.js v2.0.4
 * https://zenorocha.github.io/clipboard.js
 *
 * Licensed MIT © Zeno Rocha
 */
!(function (t, e) {
  "object" == typeof exports && "object" == typeof module
    ? (module.exports = e())
    : "function" == typeof define && define.amd
    ? define([], e)
    : "object" == typeof exports
    ? (exports.ClipboardJS = e())
    : (t.ClipboardJS = e());
})(this, function () {
  return (function (n) {
    var o = {};

    function r(t) {
      if (o[t]) return o[t].exports;
      var e = (o[t] = {
        i: t,
        l: !1,
        exports: {},
      });
      return n[t].call(e.exports, e, e.exports, r), (e.l = !0), e.exports;
    }
    return (
      (r.m = n),
      (r.c = o),
      (r.d = function (t, e, n) {
        r.o(t, e) ||
          Object.defineProperty(t, e, {
            enumerable: !0,
            get: n,
          });
      }),
      (r.r = function (t) {
        "undefined" != typeof Symbol &&
          Symbol.toStringTag &&
          Object.defineProperty(t, Symbol.toStringTag, {
            value: "Module",
          }),
          Object.defineProperty(t, "__esModule", {
            value: !0,
          });
      }),
      (r.t = function (e, t) {
        if ((1 & t && (e = r(e)), 8 & t)) return e;
        if (4 & t && "object" == typeof e && e && e.__esModule) return e;
        var n = Object.create(null);
        if (
          (r.r(n),
          Object.defineProperty(n, "default", {
            enumerable: !0,
            value: e,
          }),
          2 & t && "string" != typeof e)
        )
          for (var o in e)
            r.d(
              n,
              o,
              function (t) {
                return e[t];
              }.bind(null, o)
            );
        return n;
      }),
      (r.n = function (t) {
        var e =
          t && t.__esModule
            ? function () {
                return t.default;
              }
            : function () {
                return t;
              };
        return r.d(e, "a", e), e;
      }),
      (r.o = function (t, e) {
        return Object.prototype.hasOwnProperty.call(t, e);
      }),
      (r.p = ""),
      r((r.s = 0))
    );
  })([
    function (t, e, n) {
      "use strict";
      var r =
          "function" == typeof Symbol && "symbol" == typeof Symbol.iterator
            ? function (t) {
                return typeof t;
              }
            : function (t) {
                return t &&
                  "function" == typeof Symbol &&
                  t.constructor === Symbol &&
                  t !== Symbol.prototype
                  ? "symbol"
                  : typeof t;
              },
        i = (function () {
          function o(t, e) {
            for (var n = 0; n < e.length; n++) {
              var o = e[n];
              (o.enumerable = o.enumerable || !1),
                (o.configurable = !0),
                "value" in o && (o.writable = !0),
                Object.defineProperty(t, o.key, o);
            }
          }
          return function (t, e, n) {
            return e && o(t.prototype, e), n && o(t, n), t;
          };
        })(),
        a = o(n(1)),
        c = o(n(3)),
        u = o(n(4));

      function o(t) {
        return t && t.__esModule
          ? t
          : {
              default: t,
            };
      }
      var l = (function (t) {
        function o(t, e) {
          !(function (t, e) {
            if (!(t instanceof e))
              throw new TypeError("Cannot call a class as a function");
          })(this, o);
          var n = (function (t, e) {
            if (!t)
              throw new ReferenceError(
                "this hasn't been initialised - super() hasn't been called"
              );
            return !e || ("object" != typeof e && "function" != typeof e)
              ? t
              : e;
          })(this, (o.__proto__ || Object.getPrototypeOf(o)).call(this));
          return n.resolveOptions(e), n.listenClick(t), n;
        }
        return (
          (function (t, e) {
            if ("function" != typeof e && null !== e)
              throw new TypeError(
                "Super expression must either be null or a function, not " +
                  typeof e
              );
            (t.prototype = Object.create(e && e.prototype, {
              constructor: {
                value: t,
                enumerable: !1,
                writable: !0,
                configurable: !0,
              },
            })),
              e &&
                (Object.setPrototypeOf
                  ? Object.setPrototypeOf(t, e)
                  : (t.__proto__ = e));
          })(o, c.default),
          i(
            o,
            [
              {
                key: "resolveOptions",
                value: function () {
                  var t =
                    0 < arguments.length && void 0 !== arguments[0]
                      ? arguments[0]
                      : {};
                  (this.action =
                    "function" == typeof t.action
                      ? t.action
                      : this.defaultAction),
                    (this.target =
                      "function" == typeof t.target
                        ? t.target
                        : this.defaultTarget),
                    (this.text =
                      "function" == typeof t.text ? t.text : this.defaultText),
                    (this.container =
                      "object" === r(t.container)
                        ? t.container
                        : document.body);
                },
              },
              {
                key: "listenClick",
                value: function (t) {
                  var e = this;
                  this.listener = (0, u.default)(t, "click", function (t) {
                    return e.onClick(t);
                  });
                },
              },
              {
                key: "onClick",
                value: function (t) {
                  var e = t.delegateTarget || t.currentTarget;
                  this.clipboardAction && (this.clipboardAction = null),
                    (this.clipboardAction = new a.default({
                      action: this.action(e),
                      target: this.target(e),
                      text: this.text(e),
                      container: this.container,
                      trigger: e,
                      emitter: this,
                    }));
                },
              },
              {
                key: "defaultAction",
                value: function (t) {
                  return s("action", t);
                },
              },
              {
                key: "defaultTarget",
                value: function (t) {
                  var e = s("target", t);
                  if (e) return document.querySelector(e);
                },
              },
              {
                key: "defaultText",
                value: function (t) {
                  return s("text", t);
                },
              },
              {
                key: "destroy",
                value: function () {
                  this.listener.destroy(),
                    this.clipboardAction &&
                      (this.clipboardAction.destroy(),
                      (this.clipboardAction = null));
                },
              },
            ],
            [
              {
                key: "isSupported",
                value: function () {
                  var t =
                      0 < arguments.length && void 0 !== arguments[0]
                        ? arguments[0]
                        : ["copy", "cut"],
                    e = "string" == typeof t ? [t] : t,
                    n = !!document.queryCommandSupported;
                  return (
                    e.forEach(function (t) {
                      n = n && !!document.queryCommandSupported(t);
                    }),
                    n
                  );
                },
              },
            ]
          ),
          o
        );
      })();

      function s(t, e) {
        var n = "data-clipboard-" + t;
        if (e.hasAttribute(n)) return e.getAttribute(n);
      }
      t.exports = l;
    },
    function (t, e, n) {
      "use strict";
      var o,
        r =
          "function" == typeof Symbol && "symbol" == typeof Symbol.iterator
            ? function (t) {
                return typeof t;
              }
            : function (t) {
                return t &&
                  "function" == typeof Symbol &&
                  t.constructor === Symbol &&
                  t !== Symbol.prototype
                  ? "symbol"
                  : typeof t;
              },
        i = (function () {
          function o(t, e) {
            for (var n = 0; n < e.length; n++) {
              var o = e[n];
              (o.enumerable = o.enumerable || !1),
                (o.configurable = !0),
                "value" in o && (o.writable = !0),
                Object.defineProperty(t, o.key, o);
            }
          }
          return function (t, e, n) {
            return e && o(t.prototype, e), n && o(t, n), t;
          };
        })(),
        a = n(2),
        c =
          (o = a) && o.__esModule
            ? o
            : {
                default: o,
              };
      var u = (function () {
        function e(t) {
          !(function (t, e) {
            if (!(t instanceof e))
              throw new TypeError("Cannot call a class as a function");
          })(this, e),
            this.resolveOptions(t),
            this.initSelection();
        }
        return (
          i(e, [
            {
              key: "resolveOptions",
              value: function () {
                var t =
                  0 < arguments.length && void 0 !== arguments[0]
                    ? arguments[0]
                    : {};
                (this.action = t.action),
                  (this.container = t.container),
                  (this.emitter = t.emitter),
                  (this.target = t.target),
                  (this.text = t.text),
                  (this.trigger = t.trigger),
                  (this.selectedText = "");
              },
            },
            {
              key: "initSelection",
              value: function () {
                this.text
                  ? this.selectFake()
                  : this.target && this.selectTarget();
              },
            },
            {
              key: "selectFake",
              value: function () {
                var t = this,
                  e = "rtl" == document.documentElement.getAttribute("dir");
                this.removeFake(),
                  (this.fakeHandlerCallback = function () {
                    return t.removeFake();
                  }),
                  (this.fakeHandler =
                    this.container.addEventListener(
                      "click",
                      this.fakeHandlerCallback
                    ) || !0),
                  (this.fakeElem = document.createElement("textarea")),
                  (this.fakeElem.style.fontSize = "12pt"),
                  (this.fakeElem.style.border = "0"),
                  (this.fakeElem.style.padding = "0"),
                  (this.fakeElem.style.margin = "0"),
                  (this.fakeElem.style.position = "absolute"),
                  (this.fakeElem.style[e ? "right" : "left"] = "-9999px");
                var n =
                  window.pageYOffset || document.documentElement.scrollTop;
                (this.fakeElem.style.top = n + "px"),
                  this.fakeElem.setAttribute("readonly", ""),
                  (this.fakeElem.value = this.text),
                  this.container.appendChild(this.fakeElem),
                  (this.selectedText = (0, c.default)(this.fakeElem)),
                  this.copyText();
              },
            },
            {
              key: "removeFake",
              value: function () {
                this.fakeHandler &&
                  (this.container.removeEventListener(
                    "click",
                    this.fakeHandlerCallback
                  ),
                  (this.fakeHandler = null),
                  (this.fakeHandlerCallback = null)),
                  this.fakeElem &&
                    (this.container.removeChild(this.fakeElem),
                    (this.fakeElem = null));
              },
            },
            {
              key: "selectTarget",
              value: function () {
                (this.selectedText = (0, c.default)(this.target)),
                  this.copyText();
              },
            },
            {
              key: "copyText",
              value: function () {
                var e = void 0;
                try {
                  e = document.execCommand(this.action);
                } catch (t) {
                  e = !1;
                }
                this.handleResult(e);
              },
            },
            {
              key: "handleResult",
              value: function (t) {
                this.emitter.emit(t ? "success" : "error", {
                  action: this.action,
                  text: this.selectedText,
                  trigger: this.trigger,
                  clearSelection: this.clearSelection.bind(this),
                });
              },
            },
            {
              key: "clearSelection",
              value: function () {
                this.trigger && this.trigger.focus(),
                  window.getSelection().removeAllRanges();
              },
            },
            {
              key: "destroy",
              value: function () {
                this.removeFake();
              },
            },
            {
              key: "action",
              set: function () {
                var t =
                  0 < arguments.length && void 0 !== arguments[0]
                    ? arguments[0]
                    : "copy";
                if (
                  ((this._action = t),
                  "copy" !== this._action && "cut" !== this._action)
                )
                  throw new Error(
                    'Invalid "action" value, use either "copy" or "cut"'
                  );
              },
              get: function () {
                return this._action;
              },
            },
            {
              key: "target",
              set: function (t) {
                if (void 0 !== t) {
                  if (
                    !t ||
                    "object" !== (void 0 === t ? "undefined" : r(t)) ||
                    1 !== t.nodeType
                  )
                    throw new Error(
                      'Invalid "target" value, use a valid Element'
                    );
                  if ("copy" === this.action && t.hasAttribute("disabled"))
                    throw new Error(
                      'Invalid "target" attribute. Please use "readonly" instead of "disabled" attribute'
                    );
                  if (
                    "cut" === this.action &&
                    (t.hasAttribute("readonly") || t.hasAttribute("disabled"))
                  )
                    throw new Error(
                      'Invalid "target" attribute. You can\'t cut text from elements with "readonly" or "disabled" attributes'
                    );
                  this._target = t;
                }
              },
              get: function () {
                return this._target;
              },
            },
          ]),
          e
        );
      })();
      t.exports = u;
    },
    function (t, e) {
      t.exports = function (t) {
        var e;
        if ("SELECT" === t.nodeName) t.focus(), (e = t.value);
        else if ("INPUT" === t.nodeName || "TEXTAREA" === t.nodeName) {
          var n = t.hasAttribute("readonly");
          n || t.setAttribute("readonly", ""),
            t.select(),
            t.setSelectionRange(0, t.value.length),
            n || t.removeAttribute("readonly"),
            (e = t.value);
        } else {
          t.hasAttribute("contenteditable") && t.focus();
          var o = window.getSelection(),
            r = document.createRange();
          r.selectNodeContents(t),
            o.removeAllRanges(),
            o.addRange(r),
            (e = o.toString());
        }
        return e;
      };
    },
    function (t, e) {
      function n() {}
      (n.prototype = {
        on: function (t, e, n) {
          var o = this.e || (this.e = {});
          return (
            (o[t] || (o[t] = [])).push({
              fn: e,
              ctx: n,
            }),
            this
          );
        },
        once: function (t, e, n) {
          var o = this;

          function r() {
            o.off(t, r), e.apply(n, arguments);
          }
          return (r._ = e), this.on(t, r, n);
        },
        emit: function (t) {
          for (
            var e = [].slice.call(arguments, 1),
              n = ((this.e || (this.e = {}))[t] || []).slice(),
              o = 0,
              r = n.length;
            o < r;
            o++
          )
            n[o].fn.apply(n[o].ctx, e);
          return this;
        },
        off: function (t, e) {
          var n = this.e || (this.e = {}),
            o = n[t],
            r = [];
          if (o && e)
            for (var i = 0, a = o.length; i < a; i++)
              o[i].fn !== e && o[i].fn._ !== e && r.push(o[i]);
          return r.length ? (n[t] = r) : delete n[t], this;
        },
      }),
        (t.exports = n);
    },
    function (t, e, n) {
      var d = n(5),
        h = n(6);
      t.exports = function (t, e, n) {
        if (!t && !e && !n) throw new Error("Missing required arguments");
        if (!d.string(e))
          throw new TypeError("Second argument must be a String");
        if (!d.fn(n)) throw new TypeError("Third argument must be a Function");
        if (d.node(t))
          return (
            (s = e),
            (f = n),
            (l = t).addEventListener(s, f),
            {
              destroy: function () {
                l.removeEventListener(s, f);
              },
            }
          );
        if (d.nodeList(t))
          return (
            (a = t),
            (c = e),
            (u = n),
            Array.prototype.forEach.call(a, function (t) {
              t.addEventListener(c, u);
            }),
            {
              destroy: function () {
                Array.prototype.forEach.call(a, function (t) {
                  t.removeEventListener(c, u);
                });
              },
            }
          );
        if (d.string(t))
          return (o = t), (r = e), (i = n), h(document.body, o, r, i);
        throw new TypeError(
          "First argument must be a String, HTMLElement, HTMLCollection, or NodeList"
        );
        var o, r, i, a, c, u, l, s, f;
      };
    },
    function (t, n) {
      (n.node = function (t) {
        return void 0 !== t && t instanceof HTMLElement && 1 === t.nodeType;
      }),
        (n.nodeList = function (t) {
          var e = Object.prototype.toString.call(t);
          return (
            void 0 !== t &&
            ("[object NodeList]" === e || "[object HTMLCollection]" === e) &&
            "length" in t &&
            (0 === t.length || n.node(t[0]))
          );
        }),
        (n.string = function (t) {
          return "string" == typeof t || t instanceof String;
        }),
        (n.fn = function (t) {
          return "[object Function]" === Object.prototype.toString.call(t);
        });
    },
    function (t, e, n) {
      var a = n(7);

      function i(t, e, n, o, r) {
        var i = function (e, n, t, o) {
          return function (t) {
            (t.delegateTarget = a(t.target, n)),
              t.delegateTarget && o.call(e, t);
          };
        }.apply(this, arguments);
        return (
          t.addEventListener(n, i, r),
          {
            destroy: function () {
              t.removeEventListener(n, i, r);
            },
          }
        );
      }
      t.exports = function (t, e, n, o, r) {
        return "function" == typeof t.addEventListener
          ? i.apply(null, arguments)
          : "function" == typeof n
          ? i.bind(null, document).apply(null, arguments)
          : ("string" == typeof t && (t = document.querySelectorAll(t)),
            Array.prototype.map.call(t, function (t) {
              return i(t, e, n, o, r);
            }));
      };
    },
    function (t, e) {
      if ("undefined" != typeof Element && !Element.prototype.matches) {
        var n = Element.prototype;
        n.matches =
          n.matchesSelector ||
          n.mozMatchesSelector ||
          n.msMatchesSelector ||
          n.oMatchesSelector ||
          n.webkitMatchesSelector;
      }
      t.exports = function (t, e) {
        for (; t && 9 !== t.nodeType; ) {
          if ("function" == typeof t.matches && t.matches(e)) return t;
          t = t.parentNode;
        }
      };
    },
  ]);
});
