(function ($) {

  'use strict';

  $(document).ready(function () {
    transfersOptionsFramework.init();
  });

  var transfersOptionsFramework = {

    init: function () {

      $(".create_transfers_tables").on('click', function (e) {

        var parentDiv = $(this).parent();
        var loadingDiv = parentDiv.find('.loading');
        loadingDiv.show();
        var _wpnonce = $('#_wpnonce').val();

        var dataObj = {
          'action': 'transfers_extra_tables_ajax_request',
          'nonce': _wpnonce
        }

        $.ajax({
          url: window.adminAjaxUrl,
          data: dataObj,
          success: function (json) {
            // This outputs the result of the ajax request
            loadingDiv.hide();
            window.location = window.adminSiteUrl;
          },
          error: function (errorThrown) {
            console.log(errorThrown);
          }
        });

        e.preventDefault();
      });

      $('.input-label-for-dynamic-id').each(function (index, element) {
        transfersOptionsFramework.bindLabelForDynamicIdField($(this));
      });

      transfersOptionsFramework.bindDynamicIdField($('.input-dynamic-id'));
      transfersOptionsFramework.bindModifyDynamicIdCheckbox($('.modify-dynamic-element-id'));
      transfersOptionsFramework.bindRemoveIcons();

      transfersOptionsFramework.initializeOptionsTab('services', 'enable_services');
      transfersOptionsFramework.initializeOptionsTab('faqs', 'enable_faqs');
      transfersOptionsFramework.initializeOptionsTab('destinations', 'enable_destinations');
      transfersOptionsFramework.initializeOptionsTab('transporttypes', 'enable_transport_types');
      transfersOptionsFramework.initializeOptionsTab('woocommercesettings', 'use_woocommerce_for_checkout');

      $('.of-repeat-extra-fields').sortable({

        update: function (event, ui) {

          var $fieldLoop = $(this).closest('.section').find('.of-repeat-extra-fields');

          $fieldLoop.find('.of-repeat-group').each(function (index, element) {

            var $inputFieldId = $(this).find("input.input-field-id");
            var $inputFieldLabel = $(this).find("input.input-field-label");
            var $selectFieldType = $(this).find("select.select-field-type");
            var $checkboxFieldHide = $(this).find("input.checkbox-field-hide");
            var $checkboxFieldModify = $(this).find("input.checkbox-field-modify");
            var $inputFieldIndex = $(this).find("input.input-index");

            var $labelFieldId = $(this).find("label.label-field-id");
            var $labelFieldLabel = $(this).find("label.label-field-label");
            var $labelFieldType = $(this).find("label.label-field-type");
            var $labelFieldHide = $(this).find("label.label-field-hide");
            var $labelFieldModify = $(this).find("label.label-field-modify");

            $inputFieldId.attr("name", $inputFieldId.attr("data-rel") + "[" + index + "][id]");
            $labelFieldId.attr("for", $inputFieldId.attr("data-rel") + "[" + index + "][id]");
            $inputFieldLabel.attr("name", $inputFieldLabel.attr("data-rel") + "[" + index + "][label]");
            $labelFieldLabel.attr("for", $inputFieldLabel.attr("data-rel") + "[" + index + "][label]");
            $selectFieldType.attr("name", $selectFieldType.attr("data-rel") + "[" + index + "][type]");
            $labelFieldType.attr("for", $selectFieldType.attr("data-rel") + "[" + index + "][type]");
            $checkboxFieldHide.attr("name", $checkboxFieldHide.attr("data-rel") + "[" + index + "][hide]");
            $labelFieldHide.attr("for", $checkboxFieldHide.attr("data-rel") + "[" + index + "][hide]");
            $checkboxFieldModify.attr("name", $checkboxFieldModify.attr("data-rel") + "[" + index + "][modify]");
            $labelFieldModify.attr("for", $checkboxFieldModify.attr("data-rel") + "[" + index + "][modify]");
            $inputFieldIndex.attr("name", $inputFieldIndex.attr("data-rel") + "[" + index + "][index]");
            $inputFieldIndex.val(index);
          });
        }
      });

      $('.of-repeat-form-fields').sortable({

        update: function (event, ui) {

          var $fieldLoop = $(this).closest('.section').find('.of-repeat-form-fields');

          $fieldLoop.find('.of-repeat-group').each(function (index, element) {

            var $inputFieldId = $(this).find("input.input-field-id");
            var $inputFieldLabel = $(this).find("input.input-field-label");
            var $labelFieldType = $(this).find("label.label-field-type");
            var $selectFieldType = $(this).find("select.select-field-type");
            var $labelFieldHide = $(this).find("label.label-field-hide");
            var $labelFieldModify = $(this).find("label.label-field-modify");
            var $checkboxFieldHide = $(this).find("input.checkbox-field-hide");
            var $checkboxFieldModify = $(this).find("input.checkbox-field-modify");
            var $inputFieldIndex = $(this).find("input.input-index");

            $inputFieldId.attr("name", $inputFieldId.attr("data-rel") + "[" + index + "][id]");
            $inputFieldLabel.attr("name", $inputFieldLabel.attr("data-rel") + "[" + index + "][label]");
            $selectFieldType.attr("name", $selectFieldType.attr("data-rel") + "[" + index + "][type]");
            $labelFieldType.attr("for", $selectFieldType.attr("data-rel") + "[" + index + "][type]");
            $checkboxFieldHide.attr("name", $checkboxFieldHide.attr("data-rel") + "[" + index + "][hide]");
            $labelFieldHide.attr("for", $checkboxFieldHide.attr("data-rel") + "[" + index + "][hide]");
            $checkboxFieldModify.attr("name", $checkboxFieldModify.attr("data-rel") + "[" + index + "][modify]");
            $labelFieldModify.attr("for", $checkboxFieldModify.attr("data-rel") + "[" + index + "][modify]");
            $inputFieldIndex.attr("name", $inputFieldIndex.attr("data-rel") + "[" + index + "][index]");
            $inputFieldIndex.val(index);
          });
        }
      });

      $('.docopy_field').on('click', function (e) {

        var $section = $(this).closest(".section");
        var $loop = $section.find(".of-repeat-extra-fields");
        var $toCopy = $loop.find(".of-repeat-group:last");
        var $newGroup = $toCopy.clone();
        var maxFieldIndex = parseInt($section.find(".max_field_index").val(), 10) + 1;

        $newGroup.insertAfter($toCopy);

        $section.find(".max_field_index").val(maxFieldIndex);
        $newGroup.find("input.input-index").val(maxFieldIndex);

        transfersOptionsFramework.initializeCustomField(".input-field-label", "label", $newGroup, maxFieldIndex, "");
        transfersOptionsFramework.initializeCustomField(".input-field-id", "id", $newGroup, maxFieldIndex, "");
        transfersOptionsFramework.initializeCustomField(".input-index", "index", $newGroup, maxFieldIndex, "");
        transfersOptionsFramework.initializeCustomField(".select-field-type", "type", $newGroup, maxFieldIndex, "");

        transfersOptionsFramework.initializeCustomField(".checkbox-field-hide", "hide", $newGroup, maxFieldIndex, "label.label-field-hide");
        transfersOptionsFramework.initializeCustomField(".checkbox-field-modify", "modify", $newGroup, maxFieldIndex, "label.label-field-modify");

        $newGroup.append($("<span class=\"ui-icon ui-icon-close\"></span>"));

        transfersOptionsFramework.bindRemoveIcons();
        transfersOptionsFramework.bindLabelForDynamicIdField($newGroup.find("input.input-field-label"));
        transfersOptionsFramework.bindDynamicIdField($newGroup.find("input.input-field-id"));
        transfersOptionsFramework.bindModifyDynamicIdCheckbox($newGroup.find("input.modify-dynamic-element-id"));

        e.preventDefault();
      });

      $('.docopy_form_field').on('click', function (e) {

        var $section = $(this).closest(".section");
        var $loop = $section.find(".of-repeat-form-fields");
        var $toCopy = $loop.find(".of-repeat-group:last");
        var $newGroup = $toCopy.clone();
        var maxFieldIndex = parseInt($section.find(".max_field_index").val(), 10) + 1;

        $newGroup.insertAfter($toCopy);

        $section.find('.max_field_index').val(maxFieldIndex);
        $newGroup.find('input.input-index').val(maxFieldIndex);

        transfersOptionsFramework.initializeCustomField(".input-field-label", "label", $newGroup, maxFieldIndex, "");
        transfersOptionsFramework.initializeCustomField(".input-field-id", "id", $newGroup, maxFieldIndex, "");
        transfersOptionsFramework.initializeCustomField(".input-index", "index", $newGroup, maxFieldIndex, "");
        transfersOptionsFramework.initializeCustomField(".select-field-type", "type", $newGroup, maxFieldIndex, "");
        transfersOptionsFramework.initializeCustomField(".checkbox-field-hide", "hide", $newGroup, maxFieldIndex, "label.label-field-hide");
        transfersOptionsFramework.initializeCustomField(".checkbox-field-modify", "modify", $newGroup, maxFieldIndex, "label.label-field-modify");
        transfersOptionsFramework.initializeCustomField(".checkbox-field-required", "required", $newGroup, maxFieldIndex, "label.label-field-required");

        $newGroup.append($('<span class="ui-icon ui-icon-close"></span>'));

        transfersOptionsFramework.bindRemoveIcons();
        transfersOptionsFramework.bindLabelForDynamicIdField($newGroup.find('input.input-field-label'));
        transfersOptionsFramework.bindDynamicIdField($newGroup.find('input.input-field-id'));
        transfersOptionsFramework.bindModifyDynamicIdCheckbox($newGroup.find('input.modify-dynamic-element-id'));

        e.preventDefault();
      });

    },
    initializeOptionsTab: function (groupClass, checkboxId) {
      transfersOptionsFramework.toggleTabVisibility($("#" + checkboxId).is(':checked'), groupClass, checkboxId);

      $("#" + checkboxId).change(function () {
        transfersOptionsFramework.toggleTabVisibility(this.checked, groupClass, checkboxId);
      });
    },
    toggleTabVisibility: function (show, groupClass, checkboxId) {
      if (show) {
        $(".group." + groupClass).children().show();
      } else {
        $(".group." + groupClass).children().hide();
        $("#section-" + checkboxId).show();
        $(".group." + groupClass + " > h3").show();
        $("#section-" + checkboxId).children().show();
      }
    },
    bindModifyDynamicIdCheckbox: function ($checkboxInput) {

      $checkboxInput.on("click", function () {

        var $idInput = $(this).parent().parent().prevAll(".of-input-wrap.of-modify-id").find("input[type=text].input-dynamic-id");

        if ($idInput.is("[readonly]")) {
          $idInput.prop("readonly", false);
        } else {
          $idInput.prop("readonly", true);
        }
      });

    },
    bindDynamicIdField: function ($inputDynamicId) {

      $inputDynamicId.on('blur', function (e) {

        if (!$(this).is('[readonly]')) {

          var $this = $(this);
          var $parentDiv = $(this).parent().parent();
          var $loadingDiv = $parentDiv.find(".loading");
          var elementType = "";
          var elementNewId = $(this).val();
          var elementId = $(this).data("id");
          var elementOriginalId = $(this).data("original-id");
          var elementIsDefault = $(this).data("is-default");

          if (elementNewId !== elementOriginalId && elementNewId != elementId && !elementIsDefault) {

            elementType = 'field';

            $loadingDiv.show();

            var newId = transfersOptionsFramework.getUniqueDynamicElementId(elementNewId, elementType, $this.data('parent'));

            $this.val(newId);
            $this.data('id', newId);

            $loadingDiv.hide();
          }
        }
      });
    },
    bindLabelForDynamicIdField: function ($inputElement) {

      var elementOriginalId = $inputElement.data('original-id');

      $inputElement.on('blur', function (e) {

        var val = $inputElement.val();
        var $parentDiv = $inputElement.parent().parent();
        var $loadingDiv = $parentDiv.find(".loading");
        var $idInput = $parentDiv.find(".input-dynamic-id");
        var elementType = "";
        var elementNewId = transfersOptionsFramework.cleanUpId(val);
        var elementIsDefault = $(this).data("is-default");

        if (!elementIsDefault && (elementOriginalId === null || typeof (elementOriginalId) === 'undefined' || elementOriginalId != elementNewId)) {

          $loadingDiv.show();

          elementType = 'field';

          var newId = transfersOptionsFramework.getUniqueDynamicElementId(elementNewId, elementType, $idInput.data('parent'));

          $idInput.val(newId);
          $idInput.data('id', newId);
          $loadingDiv.hide();
        }
      });

      if (elementOriginalId === null || typeof (elementOriginalId) === 'undefined') {

        $inputElement.on('keyup', function (e) {

          if (e.which == 13) {
            // Enter key pressed
            e.preventDefault();
          } else {

            var val = $inputElement.val();
            var $parentDiv = $inputElement.parent().parent();
            var $idInput = $parentDiv.find(".input-dynamic-id");
            var slug = transfersOptionsFramework.cleanUpId(val);

            $idInput.val(slug);
          }
        });

      }
    },
    getUniqueDynamicElementId: function (elementNewId, elementType, parent) {

      var newId = '';

      var dataObj = {
        'action': 'generate_unique_dynamic_element_id',
        'element_id': elementNewId,
        'nonce': $('#_wpnonce').val(),
        'element_type': elementType,
        'parent': parent
      };

      $.ajax({
        url: window.adminAjaxUrl,
        data: dataObj,
        async: false,
        success: function (data) {
          newId = JSON.parse(data);
        },
        error: function (errorThrown) {

        }
      });

      return newId;
    },
    cleanUpId: function (str) {
      return str.replace(/-/g, "_")
        .replace(/\s/g, "_")
        .replace(/:/g, "_")
        .replace(/\\/g, "_")
        .replace(/\//g, "_")
        .replace(/[^a-zA-Z0-9_]+/g, "")
        .replace(/-{2,}/g, "_")
        .toLowerCase();
    },
    bindRemoveIcons: function () {

      $(".ui-icon-close").unbind("click");
      $(".ui-icon-close").on("click", function () {
        $(this).parent().remove();
        return false;
      });
    },
    initializeCustomField: function (fieldSelector, fieldKey, $groupObj, fieldIndex, labelSelector) {

      var $fieldControl = $groupObj.find(fieldSelector);

      if ($fieldControl !== undefined && $fieldControl.length > 0) {
        $fieldControl.attr('name', $fieldControl.attr('data-rel') + '[' + fieldIndex + '][' + fieldKey + ']');
        $fieldControl.attr('id', $fieldControl.attr('data-rel') + '[' + fieldIndex + '][' + fieldKey + ']');

        var fieldType = $fieldControl[0].type || $fieldControl[0].tagName.toLowerCase();
        if (fieldType == 'text') {
          $fieldControl.val('');
        }

        if ($fieldControl.attr('data-original-id')) {
          $fieldControl.removeAttr('data-original-id');
        }

        if (labelSelector.length > 0) {
          $groupObj.find(labelSelector).attr('for', $fieldControl.attr('data-rel') + '[' + fieldIndex + '][' + fieldKey + ']');
        }
      }
    }
  };

})(jQuery);
