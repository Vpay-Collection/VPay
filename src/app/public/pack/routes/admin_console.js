route("admin/console/index", {
    depends:"admin/main/console",
    reference:"",
    container:"#container",
    title: "控制台",
    chartInstance:null,
    onenter: function (query, dom,result) {
       replaceTpl(dom,result.data);
    },
    onrender: function (query, dom,result) {

        const dataLine = {
            type: 'line',
            data: {
                labels: result.data.day,
                datasets: [
                    {
                        label: '收入统计',
                        data: result.data.data,
                    },
                ],
            },
        };

       this.chartInstance = new mdb.Chart(document.getElementById('canvas'), dataLine);

       var html = "";
       $.each(result.data.payments,function (k,order) {
           html+=`
          <tr>
                                <td>${order['app_name']}</td>
                                <td>${order['app_item']}</td>
                                <td>￥${order['real_price']}</td>
                                <td>${mdbAdmin.dateFormat("yyyy-MM-dd hh:mm:ss",order['pay_time'])}</td>
                            </tr>
           `;
       });
       dom.find("tbody").html(html);
    },
    onexit: function () {
        if(this.chartInstance){
            this.chartInstance.dispose();
        }
    },
});
