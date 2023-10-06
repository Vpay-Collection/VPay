mdbAdmin.loading = {
    show(parent, msg) {
        parent = parent || "#app";
        msg = msg || "";
        loadingIndex++;
        var id = "loading-" + (loadingIndex + 1).toString();
        const loader = `
    <div class="loading-delay ankio-loading" id="${id}" >
      <div class="spinner-border loading-icon text-succes"></div>
        <span class="loading-text">${msg}</span>
      </div>
`;

        $(parent).append(loader);
        resetTheme();
        /* jshint -W031 */
        new mdb.Loading(document.querySelector("#" + id), {
            backdropID: "backdrop-" + id
        });
        /* jshint +W031 */
        return id;
    },
    hide(id) {
        id = id || "loading-" + loadingIndex.toString();
        if (document.querySelector("#" + id) == null) {
            let count = 1000;
            const intval = setInterval(function() {
                if (count <= 0) {
                    clearInterval(intval);
                }
                count = count - 1;
                let elem = $(".ankio-loading");
                if (elem.length > 0) {
                    elem.remove();
                    $(".loading-backdrop").remove();
                    clearInterval(intval);
                }

            }, 200);
        } else {
            document.querySelector("#" + id).style.opacity = "0";
            setTimeout(function() {
                $("#" + id).remove();
            }, 500);
            $("#backdrop-" + id).remove();
        }

    }
};