function setMode() {
    resourceLoader.module("sidenav", function () {
        let sidenavInstance = mdb.Sidenav.getOrCreateInstance(document.getElementById("sidenav-1"));
        if (sidenavInstance === null) {
            return;
        }
        // Check necessary for Android devices
        if (window.innerWidth < 1400) {
            sidenavInstance.changeMode("over");
            sidenavInstance.hide();
        } else {
            sidenavInstance.changeMode("side");
            sidenavInstance.show();
        }
    });
}

route("", {
    title: "Vpay",
    depends: ["index/main/config", "admin/main/menu"],
    onenter: function (query, dom, result) {
        if (!result[0].data.login) {
            location.href = result[1].msg;
            return true;
        }

        var data = result[1];
        window.isManager = data.data.user.role === "1";
        var html = "";
        var menu = data.data.menu;
        $.each(menu, function (k, v) {
            if (v.child !== undefined) {
                html += `
          <li class="sidenav-item">
          <a class="sidenav-link" href="javascript:void(0)"><i class="${v.icon} fa-fw me-3"></i>${v.name}</a>
          <ul class="sidenav-collapse">
          `;
                $.each(v.child, function (kk, vv) {

                    html += `
            <li class="sidenav-item m-2 ">
              <a class="sidenav-link pt-3 pb-3" 
              href="${!vv.href.startsWith("http") ? "/@" + vv.href : vv.href}" 
                ${!vv.href.startsWith("http") ? "data-link=\"true\"" : "target=\"_blank\""}>
                <i class="${vv.icon} fa-fw me-3"></i>${vv.name}
                </a>
            </li>
          `;
                });
                html += `
        </ul>
        </li>`;
            } else {
                html += `
          <li class="sidenav-item">
          <a class="sidenav-link" 
          href="${!v.href.startsWith("http") ? "/@" + v.href : v.href}" 
             ${!v.href.startsWith("http") ? "data-link=\"true\"" : "target=\"_blank\""}>
            <i class="${v.icon} fa-fw me-3"></i>${v.name}
            </a>
          </li>`;
            }

        });

        dom.find(".sidenav-menu").html(html);
        dom.find(".sidenav-menu a").off().on("click", function (e) {
            var href = $(this).attr("href");
            if (href.startsWith("/@")) {
                go(href.slice(2));
                e.stopPropagation();
                e.preventDefault();
            }
        });
        dom.find("#username").html(data.data.user.name);
        dom.find("#image").attr("src", data.data.user.image);


        if (window.location.pathname === "/@" || window.location.pathname === "/@/") {
            go(data.data.home.href);
        }


    },
    onrender: function () {
        setMode();
        window.addEventListener("resize", setMode);
    },
    onexit: function () {
        var sidenav = document.getElementById("sidenav-1");
        if (sidenav) {
            resourceLoader.module("sidenav", function () {
                mdb.Sidenav.getInstance(sidenav).dispose();
                window.removeEventListener("resize", setMode);
            });
        }

    },
});
