mdbAdmin.modal =  {
    position: {
        topRight: "modal-side  modal-top-right",
        topLeft: "modal-side modal-top-left",
        bottomRight: "modal-side  modal-bottom-right",
        bottomLeft: "modal-side  modal-bottom-right",
        center: "modal-dialog-centered modal-dialog-scrollable"
    },
    size: {
        small: "modal-sm",
        default: "",
        large: "modal-lg",
        extraLarge: "modal-xl",
        full: "modal-fullscreen"
    },
    color: {
        // primary:"bg-primary",
        primary: ["primary", "white"],
        success: ["success", "white"],
        error: ["danger", "white"],
        info: ["info", "white"],
        warning: ["warning", "white"]
    },
    show(config) {
        config = $.extend({}, {
            title: "",
            body: "",
            position: this.position.center,
            size: this.size.default,
            color: this.color.primary,
            buttons: [
                ["关闭"], ["确定"]
            ],
            onclose:function () {

            },
            oncreate:function (dom) {

            },
            onrender:function (dom) {

            }
        }, config);
        loadingIndex++;
        const id = "modal-" + loadingIndex.toString();
        var tpl = `
            <div class="modal fade" id="${id}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog  ${config["position"]} ${config["size"]}">
    <div class="modal-content">
      <div class="modal-header bg-${config["color"][0]} text-${config["color"][1]}">
        <h5 class="modal-title " >${config["title"]}</h5>
        <button type="button" class="btn-close btn-close-${config["color"][1]}" data-mdb-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">${config["body"]}</div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-${config["color"][0]}" >${config["buttons"][0][0]}</button>
        <button type="button" class="btn  btn-${config["color"][0]}" >${config["buttons"][1][0]}</button>
      </div>
    </div>
  </div>
</div>
            `;
        var dom = $(tpl);
        let func = config.oncreate;
        if (typeof func === "function") {
            func(dom);
        }
        $("body").append(dom);

        mdbAdmin.initComponents("#"+id);
        let funcs = config.onrender();
        if (typeof funcs === "function") {
            funcs(dom,id);
        }
        mdb.Modal.getOrCreateInstance(document.getElementById(id)).show();

        $("#" + id + " .modal-footer button:eq(0)").on("click", function() {
            let func = config.buttons[0][1];
            if (typeof func === "function") {
                func(id);

            }
            mdb.Modal.getOrCreateInstance(document.getElementById(id)).hide();
        });
        $("#" + id + " .modal-footer button:eq(1)").on("click", function() {
            var form = $("#" + id+" form");
            if(form.length>0){
                form.trigger("submit");
            }else{
                let func = config.buttons[1][1];
                if (typeof func === "function") {
                    func(id);
                }
                mdb.Modal.getOrCreateInstance(document.getElementById(id)).hide();
            }

        });
        resetTheme();
        document.getElementById(id).addEventListener("hidden.mdb.modal", () => {
            let func = config.onclose;
            if (typeof func === "function") {
                func(id);
            }
            document.getElementById(id).remove();
        });
    }
};