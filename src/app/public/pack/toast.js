mdbAdmin.toast = {
    success(msg, title) {
    title = title || "成功";
    this.custom(msg, title, "success");
},
error(msg, title) {
    title = title || "错误";
    this.custom(msg, title, "danger");
},
info(msg, title) {
    title = title || "提示";
    this.custom(msg, title, "info");
},
warning(msg, title) {
    title = title || "警告";
    this.custom(msg, title, "warning");
}, custom(msg, title, color) {
    loadingIndex++;
    const id = "toast-" + loadingIndex.toString();
    const tpl = `
            
    <div
     class="toast fade mx-auto"
  role="alert"
  aria-live="assertive"
  aria-atomic="true"
  data-mdb-autohide="true"
  data-mdb-delay="4000"
  data-mdb-position="top-right"
  data-mdb-append-to-body="true"
  data-mdb-stacking="true"
  data-mdb-width="250px"
  data-mdb-color="${color}"
      id="${id}"
    >
      <div class="toast-header">
        <i class="fas fa-exclamation-circle fa-lg me-2"></i>
        <strong class="me-auto">${title}</strong>
        <small></small>
        <button type="button" class="btn-close" data-mdb-dismiss="toast" aria-label="Close"></button>
      </div>
      <div class="toast-body">${msg}</div>
    
  </div>
            `;
    $("body").append(tpl);
    mdb.Toast.getOrCreateInstance(document.getElementById(id)).show();
    resetTheme();
    document.getElementById(id).addEventListener("hidden.mdb.toast", () => {
        document.getElementById(id).remove();
    });
}
};