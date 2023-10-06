const setMode = () => {
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
};
route("/", {
    title: "Ankioの用户中心",
    onenter: function (query, dom) {

    },
    onrender: function () {
        mdbAdmin.initAdmin("admin/main/menu",function (data) {
             if(data.code===401){
               location.href = data.msg;
            }
        },function () {
            setMode();
            window.addEventListener("resize", setMode);
        });

    },
    onexit: function () {
        //侧边栏对象销毁
        mdb.Sidenav.getInstance(document.getElementById("sidenav-1")).dispose();
        window.removeEventListener("resize", setMode);
    },
});
