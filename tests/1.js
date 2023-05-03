const allElements = document.querySelectorAll('*');
const events = {};

allElements.forEach(function (element) {
    // 获取该元素当前绑定的所有事件
    const elementEvents = window.getEventListeners(element);

    // 将事件及其处理函数保存到对象中
    for (const event in elementEvents) {
        if (elementEvents.hasOwnProperty(event)) {
            if (!events[event]) {
                events[event] = [];
            }
            elementEvents[event].forEach(function (listener) {
                events[event].push({
                    element: element,
                    listener: listener.listener
                });
            });
        }
    }
});

console.log(events);