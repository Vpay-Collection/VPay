function randomData() {
    return Math.round(Math.random() * 500);
}

function ecchartData(color) {
    color = color || "#00c292";
    return {
        color: color,
        toolbox: {
            show: false,
            feature: {
                saveAsImage: {}
            }
        },
        grid: {
            left: '-1%',
            right: '0',
            bottom: '0',
            top: '5px',
            containLabel: false
        },
        xAxis: [
            {
                type: 'category',
                boundaryGap: false,
                data: ['周一', '周二', '周三', '周四', '周五', '周六', '周日'],
                splitLine: {
                    show: false
                },
            }
        ],
        yAxis: [
            {
                type: 'value',
                splitLine: {
                    show: false
                },
            }
        ],
        series: [
            {
                name: '用户',
                type: 'line',
                stack: '总量',
                smooth: true,
                symbol: "none",
                clickable: false,
                areaStyle: {},
                data: [randomData(), randomData(), randomData(), randomData(), randomData(), randomData(), randomData()]
            }
        ]
    }
}

var echOne = {
    color: "#03a9f3",
    xAxis: {
        type: 'category',
        data: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']
    },
    yAxis: {
        type: 'value'
    },
    series: [{
        data: [120, 200, 150, 80, 70, 110, 130],
        type: 'bar'
    }]
};
var echTwo = {
    title: {
        show: false,
        text: '用户访问来源',
        subtext: '纯属虚构',
        x: 'center'
    },
    tooltip: {
        trigger: 'item',
        formatter: "{a} <br/>{b} : {c} ({d}%)"
    },
    legend: {
        orient: 'vertical',
        left: 'left',
        data: ['直接访问', '邮件营销', '联盟广告', '视频广告', '搜索引擎']
    },
    series: [
        {
            name: '访问来源',
            type: 'pie',
            radius: '55%',
            center: ['50%', '60%'],
            data: [
                {value: 335, name: '直接访问'},
                {value: 310, name: '邮件营销'},
                {value: 234, name: '联盟广告'},
                {value: 135, name: '视频广告'},
                {value: 1548, name: '搜索引擎'}
            ],
            itemStyle: {
                emphasis: {
                    shadowBlur: 10,
                    shadowOffsetX: 0,
                    shadowColor: 'rgba(0, 0, 0, 0.5)'
                }
            }
        }
    ]
};
var mapThree = {
    title: {
        show: false,
        text: '堆叠区域图xxxx'
    },
    tooltip: {
        trigger: 'axis',
        axisPointer: {
            type: 'cross',
            label: {
                backgroundColor: '#6a7985'
            }
        }
    },
    legend: {
        data: ['邮件营销', '联盟广告', '视频广告', '直接访问', '搜索引擎']
    },
    toolbox: {
        show: false,
        feature: {
            saveAsImage: {}
        }
    },
    grid: {
        left: '3%',
        right: '4%',
        bottom: '3%',
        containLabel: true
    },
    xAxis: [
        {
            type: 'category',
            boundaryGap: false,
            data: ['周一', '周二', '周三', '周四', '周五', '周六', '周日']
        }
    ],
    yAxis: [
        {
            type: 'value',
            splitLine: {//设置横线样式
                show: false//隐藏横线
            },
        }
    ],
    series: [
        {
            name: '邮件营销',
            type: 'line',
            stack: '总量',
            smooth: true,//曲线线条
            areaStyle: {},
            data: [120, 132, 101, 134, 90, 230, 210]
        },
        {
            name: '联盟广告',
            type: 'line',
            stack: '总量',
            smooth: true,//曲线线条
            areaStyle: {},
            data: [220, 182, 191, 234, 290, 330, 310]
        },
        {
            name: '视频广告',
            type: 'line',
            stack: '总量',
            smooth: true,//曲线线条
            areaStyle: {},
            data: [150, 232, 201, 154, 190, 330, 410]
        },
        {
            name: '直接访问',
            type: 'line',
            stack: '总量',
            smooth: true,//曲线线条
            areaStyle: {normal: {}},
            data: [320, 332, 301, 334, 390, 330, 320]
        },
        {
            name: '搜索引擎',
            type: 'line',
            stack: '总量',
            smooth: true,//曲线线条
            label: {
                normal: {
                    show: true,
                    position: 'top'
                }
            },
            areaStyle: {normal: {}},
            data: [370, 932, 901, 934, 1290, 1330, 1320]
        }
    ]
};

layui.define(function (exports) {
    var income = ecchartData("#00c292");
    var goods = ecchartData("#ab8ce4");
    var blogs = ecchartData("#03a9f3");
    var user = ecchartData("#fb9678");

    exports('home2Data', {
        income: income,
        goods: goods,
        blogs: blogs,
        user: user,
        echOne: echOne,
        echTwo: echTwo,
        mapThree: mapThree,
    });
});


