mdbAdmin.alert =  {
    success(msg) {
        this.custom(msg, "success");
    },
    error(msg) {
        this.custom(msg, "danger");
    },
    info(msg) {
        this.custom(msg, "info");
    },
    warning(msg) {
        this.custom(msg, "warning");
    },
    custom(msg, color) {
        app.loading = app.loading + 1;
        const id = "alert-" + app.loading.toString();
        const tpl = `
            <div
  class="alert fade"
  id="alert-primary"
  role="alert"
  data-mdb-color="${color}"
  data-mdb-position="top-right"
  data-mdb-stacking="true"
  data-mdb-width="535px"
  data-mdb-append-to-body="true"
  data-mdb-hidden="true"
  data-mdb-autohide="true"
  data-mdb-delay="4000"
>
 ${msg}
</div>
            `;
        $("body").append(tpl);
        mdb.Alert.getOrCreateInstance(document.getElementById(id)).show();
        resetTheme();
        document.getElementById(id).addEventListener("closed.mdb.alert", () => {
            document.getElementById(id).remove();
        });
    }
};