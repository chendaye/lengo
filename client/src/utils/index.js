/**
 * Created by PanJiaChen on 16/11/18.
 */

/**
 * Parse the time to string
 * @param {(Object|string|number)} time
 * @param {string} cFormat
 * @returns {string}
 */
export function parseTime(time, cFormat) {
  if (arguments.length === 0) {
    return null
  }
  const format = cFormat || '{y}-{m}-{d} {h}:{i}:{s}'
  let date
  if (typeof time === 'object') {
    date = time
  } else {
    if ((typeof time === 'string') && (/^[0-9]+$/.test(time))) {
      time = parseInt(time)
    }
    if ((typeof time === 'number') && (time.toString().length === 10)) {
      time = time * 1000
    }
    date = new Date(time)
  }
  const formatObj = {
    y: date.getFullYear(),
    m: date.getMonth() + 1,
    d: date.getDate(),
    h: date.getHours(),
    i: date.getMinutes(),
    s: date.getSeconds(),
    a: date.getDay()
  }
  const time_str = format.replace(/{(y|m|d|h|i|s|a)+}/g, (result, key) => {
    let value = formatObj[key]
    // Note: getDay() returns 0 on Sunday
    if (key === 'a') { return ['日', '一', '二', '三', '四', '五', '六'][value ] }
    if (result.length > 0 && value < 10) {
      value = '0' + value
    }
    return value || 0
  })
  return time_str
}

/**
 * @param {number} time
 * @param {string} option
 * @returns {string}
 */
export function formatTime(time, option) {
  if (('' + time).length === 10) {
    time = parseInt(time) * 1000
  } else {
    time = +time
  }
  const d = new Date(time)
  const now = Date.now()

  const diff = (now - d) / 1000

  if (diff < 30) {
    return '刚刚'
  } else if (diff < 3600) {
    // less 1 hour
    return Math.ceil(diff / 60) + '分钟前'
  } else if (diff < 3600 * 24) {
    return Math.ceil(diff / 3600) + '小时前'
  } else if (diff < 3600 * 24 * 2) {
    return '1天前'
  }
  if (option) {
    return parseTime(time, option)
  } else {
    return (
      d.getMonth() +
      1 +
      '月' +
      d.getDate() +
      '日' +
      d.getHours() +
      '时' +
      d.getMinutes() +
      '分'
    )
  }
}

/**
 * @param {string} url
 * @returns {Object}
 */
export function param2Obj(url) {
  const search = url.split('?')[1]
  if (!search) {
    return {}
  }
  return JSON.parse(
    '{"' +
      decodeURIComponent(search)
        .replace(/"/g, '\\"')
        .replace(/&/g, '","')
        .replace(/=/g, '":"')
        .replace(/\+/g, ' ') +
      '"}'
  )
}

/**
 * 根据ID获取列表中指定元素的索引值
 * @param {*} list
 * @param {*} id
 * @param {*} field
 */
export const getIndexByID = (list, id, field = 'id') => {
  for (const i in list) {
    const current = list[i];
    if (current[field] === id) {
      return i;
    }
  }
  return -1;
};

/**
 * 更新元素
 * @param {*} list
 * @param {*} item
 * @param {*} field
 */
export const updateItem = (list, item, field = 'id') => {
  const index = getIndexByID(list, item[field], field);
  if (index < 0) {
    return list.splice(0, 0, item);
  }
  return list.splice(index, 1, item);
};

/**
 * 删除元素
 * @param {*} list
 * @param {*} id
 * @param {*} field
 */
export const deleteItem = (list, id, field = 'id') => {
  const index = getIndexByID(list, id, field);
  if (index >= 0) {
    return list.splice(index, 1);
  }
};

/**
 * 通过指定字段 把数组某一个元素移动到数组最前面或者最后面
 * @param {array} list
 * @param {int} id
 * @param {bool} up
 * @param {string} field
 */
export const bubbleItem = (list, id, up = true, checked = false, field = 'id') => {
  const index = getIndexByID(list, id, field); // 找到元素对应的索引

  if (index >= 0) {
    const tmp = list[index]; // 暂存元素
    tmp.checked = checked;
    // 删除元素
    list.splice(index, 1);
    if (up) {
      // 通过id找到指定的元素，移到数组最前面
      return list.unshift(tmp);
    } else {
      // 通过id找到指定的元素，移到数组最后面
      return list.push(tmp);
    }
  }
};

// 子分类
export function son(data, arr = []) {
  if (data.children !== undefined && data.children.length > 0) {
    arr.push(data.id);
    for (const elem of data.children.values()) {
      son(elem, arr);
    }
    return arr;
  } else {
    arr.push(data.id);
    return arr;
  }
}

// 确定取消选中的标签的索引
export function findIndex(list, data) {
  let index = null;
  for (let i = 0; i < list.length; i++) {
    if (list[i] === data) {
      index = i;
    }
  }
  return index;
}
