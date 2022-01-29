/*******************************************************************************
 * Copyright (c) 2021. CleanPHP. All Rights Reserved.
 ******************************************************************************/

const ws={
    Socket : '',
    setIntervalWesocketPush: null,
    eventOpen:null,
    eventMessage:null,
    eventError:null,
    eventClose:null,
    /**
     * 建立websocket连接
     * @param {string} url ws地址
     */
    createSocket : url => {
        ws.Socket && ws.Socket.close()
        if (!ws.Socket) {
            console.log('建立websocket连接')
            ws.Socket = new WebSocket(url)
            ws.Socket.onopen = function (){
                ws.sendPing();
                if(ws.eventOpen!==null)
                    ws.eventOpen();
            };
            ws.Socket.onmessage =  function (e){
                if(e.data!=="i"){
                    if(ws.eventMessage!==null){
                        ws.eventMessage(e);
                    }

                }
            };
            ws.Socket.onerror =  function () {
                ws.Socket.close()
                clearInterval(ws.setIntervalWesocketPush)
                console.log('连接失败重连中')
                if (ws.Socket.readyState !== 3) {
                    ws.Socket = null
                    ws.createSocket()
                }
                if(ws.eventError!==null)
                    ws.eventError();
            };
            ws.Socket.onclose = function (){
                clearInterval(ws.setIntervalWesocketPush)
                console.log('websocket已断开....正在尝试重连')
                if (ws.Socket.readyState !== 2) {
                    ws.Socket = null;
                    ws.createSocket(ws.url);
                }
                if(ws.eventClose!==null)
                    ws.eventClose();
            };
        } else {
            console.log('websocket已连接')
        }
    },


    /**
     * 发送数据但连接未建立时进行处理等待重发
     * @param {any} message 需要发送的数据
     */
     connecting : function (message){
        setTimeout(() => {
            if (ws.Socket.readyState === 0) {
                ws.connecting(message)
            } else {
                ws.Socket.send(JSON.stringify(message))
            }
        }, 1000)
    },

    /**
     * 发送数据
     * @param {any} message 需要发送的数据
     */
    push :  function (message) {
        if (ws.Socket !== null && ws.Socket.readyState === 3) {
            ws.Socket.close()
            ws.createSocket()
        } else if (ws.Socket.readyState === 1) {
            ws.Socket.send(JSON.stringify(message))
        } else if (ws.Socket.readyState === 0) {
            ws.connecting(message)
        }
    },

    /**发送心跳
     * @param {number} time 心跳间隔毫秒 默认5000
     * @param {string} ping 心跳名称 默认字符串ping
     */
     sendPing : (time = 500, ping = 'i') => {
      console.log("发送ping心跳")
        clearInterval(ws.setIntervalWesocketPush)
        ws.Socket.send(ping)
        ws.setIntervalWesocketPush = setInterval(() => {
            ws.Socket.send(ping)
        }, time)
    }

}



