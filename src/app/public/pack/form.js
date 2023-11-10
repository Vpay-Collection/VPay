var form = {
	// 使用了ES5语法
	set: function (formElem, jsonData) {
		var $form = $(formElem);
		var formElements = $form.find(":input:not(:reset):not(:button):not(:submit)[name]");

		// 参数设置
		this.reset(formElem);

		if (jsonData && $.isPlainObject(jsonData) && !$.isEmptyObject(jsonData)) {
			formElements.each(function () {
				var $element = $(this);
				var name = $element.attr("name");
				var value = jsonData[name];

				if (value !== undefined) {
					if ($element.is(":file")) {
						sessionStorage.setItem("file_" + name, value);
						var instance = FileUpload.getInstance($(this)[0]) || new FileUpload($(this)[0]);
						instance.update({"defaultFile": value});
						// 确保插件加载后执行
					} else if ($element.is(":radio")) {
						$form.find(":radio[name='" + name + "']").prop("checked", false)
							.filter("[value='" + value + "']").prop("checked", true);
					} else if ($element.is(":checkbox")) {
						$element.prop("checked", false);
						if ($.isArray(value)) {
							$.each(value, function (i, val) {
								$form.find(":checkbox[name='" + name + "'][value='" + val + "']").prop("checked", true);
							});
						} else {
							$form.find(":checkbox[name='" + name + "'][value='" + value + "']").prop("checked", true);
						}
					} else {
						$element.val(value);
						if ($element.is("select")) {
							mdb.Select.getOrCreateInstance($element.get(0)).setValue(value);

						}
					}
				} else if (name.startsWith("wysiwyg-textarea")) {
					var item = $("[name=" + name + "]").next().data("name");
					if (jsonData.hasOwnProperty(item)) {
						$element.next().find(".wysiwyg-content").html(jsonData[item]);
					}
				}
			});
		}
		return $form;
	},

	get: function (formElem) {
		var jsonData = {};
		var $formElements = $(formElem).find(":input:not(:reset):not(:button):not(:submit)[name]");

		$formElements.each(function () {
			var $element = $(this);
			var name = $element.attr("name");
			var value = $element.val();

			if ($element.is(":file")) {
				jsonData[name] = sessionStorage.getItem("file_" + name);
			} else if ($element.is(":checkbox")) {
				if (!jsonData[name]) {
					jsonData[name] = $element.is(":checked") ? value : "";
				} else {
					jsonData[name] += $element.is(":checked") ? (jsonData[name] ? "," : "") + value : "";
				}
			} else if ($element.is(":radio")) {
				if ($element.is(":checked")) {
					jsonData[name] = value;
				}
			} else if ($element.is("textarea") && name.startsWith("wysiwyg-textarea")) {
				jsonData[$element.next().data("name")] = value;
			} else {
				jsonData[name] = value;
			}
		});
		return jsonData;
	},

	val: function (formElem, value) {
		if (typeof value !== "undefined") {
			this.set(formElem, value);
		} else {
			return this.get(formElem);
		}
	},

	reset: function (formElem) {
		var $form = $(formElem);
		$form.find(":input[type!='button'][type!='radio'][type!='checkbox'][type!='reset'][type!='submit']").val("");
		$form.find(":checkbox, :radio").prop("checked", false);

		$form.find("[type='file']").each(function () {
			var instance = FileUpload.getInstance($(this)[0]) || new FileUpload($(this)[0]);
			instance.update({"defaultFile": ""});
		});
		return $form;
	},

	submit: function (formElem, fn) {
		$(formElem).on("submit", function (e) {
			e.preventDefault(); // 阻止默认提交事件
			if ($.isFunction(fn)) {
				fn(form.get(formElem));
			}
		});
	},

	init: function (formElem, url, init_success, submit_success) {
		this.bindInit(formElem, url, init_success);
		this.bindSubmit(formElem, url, submit_success);
	},

	bindInit: function (formElem, url, init_success) {
		var self = this;
		request(url, {}, lang("请稍候...")).done(function (data) {
			self.val(formElem, data.data);
			if ($.isFunction(init_success)) {
				init_success(data.data);
			}
		});
	},

	bindSubmit: function (formElem, url, submit_success) {
		this.submit(formElem, function (data) {
			request(url, data, lang("正在提交...")).done(function (response) {
				mdbAdmin.toast.success(response.msg);
				if ($.isFunction(submit_success)) {
					submit_success(data);
				}
			});
		});
	}
};
