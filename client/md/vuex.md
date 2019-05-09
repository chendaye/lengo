# Vuex
<!-- TOC -->

- [Vuex](#vuex)
  - [创建一个 Store](#%E5%88%9B%E5%BB%BA%E4%B8%80%E4%B8%AA-store)
  - [State](#state)
    - [**mapState 辅助函数**](#mapstate-%E8%BE%85%E5%8A%A9%E5%87%BD%E6%95%B0)
  - [Getter](#getter)
    - [**为什么要 Getter**](#%E4%B8%BA%E4%BB%80%E4%B9%88%E8%A6%81-getter)
    - [**访问 Getter**](#%E8%AE%BF%E9%97%AE-getter)
      - [通过属性访问](#%E9%80%9A%E8%BF%87%E5%B1%9E%E6%80%A7%E8%AE%BF%E9%97%AE)
      - [**通过方法访问**](#%E9%80%9A%E8%BF%87%E6%96%B9%E6%B3%95%E8%AE%BF%E9%97%AE)
      - [**mapGetters 辅助函数**](#mapgetters-%E8%BE%85%E5%8A%A9%E5%87%BD%E6%95%B0)
  - [Mutation](#mutation)
    - [**why mutation?**](#why-mutation)
    - [**提交载荷**](#%E6%8F%90%E4%BA%A4%E8%BD%BD%E8%8D%B7)
    - [**Mutation 需遵守 Vue 的响应规则**](#mutation-%E9%9C%80%E9%81%B5%E5%AE%88-vue-%E7%9A%84%E5%93%8D%E5%BA%94%E8%A7%84%E5%88%99)
    - [**使用常量替代 Mutation 事件类型**](#%E4%BD%BF%E7%94%A8%E5%B8%B8%E9%87%8F%E6%9B%BF%E4%BB%A3-mutation-%E4%BA%8B%E4%BB%B6%E7%B1%BB%E5%9E%8B)
    - [**Mutation 必须是同步函数**](#mutation-%E5%BF%85%E9%A1%BB%E6%98%AF%E5%90%8C%E6%AD%A5%E5%87%BD%E6%95%B0)
    - [**在组件中提交 Mutation**](#%E5%9C%A8%E7%BB%84%E4%BB%B6%E4%B8%AD%E6%8F%90%E4%BA%A4-mutation)
  - [Action](#action)
    - [**mutation 与 action 的区别**](#mutation-%E4%B8%8E-action-%E7%9A%84%E5%8C%BA%E5%88%AB)
    - [**注册 action**](#%E6%B3%A8%E5%86%8C-action)
    - [**分发 action**](#%E5%88%86%E5%8F%91-action)
    - [**在组件中分发 Action**](#%E5%9C%A8%E7%BB%84%E4%BB%B6%E4%B8%AD%E5%88%86%E5%8F%91-action)
    - [**组合 Action**](#%E7%BB%84%E5%90%88-action)
    - [async / await](#async--await)
      - [async](#async)
      - [await](#await)
      - [总结](#%E6%80%BB%E7%BB%93)
  - [Module](#module)
    - [**why**](#why)
    - [**模块的局部状态**](#%E6%A8%A1%E5%9D%97%E7%9A%84%E5%B1%80%E9%83%A8%E7%8A%B6%E6%80%81)
    - [**命名空间**](#%E5%91%BD%E5%90%8D%E7%A9%BA%E9%97%B4)
    - [**在带命名空间的模块内访问全局内容（Global Assets）**](#%E5%9C%A8%E5%B8%A6%E5%91%BD%E5%90%8D%E7%A9%BA%E9%97%B4%E7%9A%84%E6%A8%A1%E5%9D%97%E5%86%85%E8%AE%BF%E9%97%AE%E5%85%A8%E5%B1%80%E5%86%85%E5%AE%B9global-assets)
    - [**带命名空间的模块注册全局 action**](#%E5%B8%A6%E5%91%BD%E5%90%8D%E7%A9%BA%E9%97%B4%E7%9A%84%E6%A8%A1%E5%9D%97%E6%B3%A8%E5%86%8C%E5%85%A8%E5%B1%80-action)
    - [**带命名空间的绑定函数**](#%E5%B8%A6%E5%91%BD%E5%90%8D%E7%A9%BA%E9%97%B4%E7%9A%84%E7%BB%91%E5%AE%9A%E5%87%BD%E6%95%B0)
    - [**插件开发者的注意事项**](#%E6%8F%92%E4%BB%B6%E5%BC%80%E5%8F%91%E8%80%85%E7%9A%84%E6%B3%A8%E6%84%8F%E4%BA%8B%E9%A1%B9)
    - [**模块动态注册**](#%E6%A8%A1%E5%9D%97%E5%8A%A8%E6%80%81%E6%B3%A8%E5%86%8C)
    - [**模块重用**](#%E6%A8%A1%E5%9D%97%E9%87%8D%E7%94%A8)
  - [项目结构](#%E9%A1%B9%E7%9B%AE%E7%BB%93%E6%9E%84)
  - [热重载](#%E7%83%AD%E9%87%8D%E8%BD%BD)

<!-- /TOC -->

## 创建一个 Store

```js
// 如果在模块化构建系统中，请确保在开头调用了 Vue.use(Vuex)

const store = new Vuex.Store({
  state: {
    count: 0
  },
  mutations: {
    increment(state) {
      state.count++;
    }
  }
});
```

---

## State

- **在 Vue 组件中获得 Vuex 状态**

> Vuex 通过 store 选项，提供了一种机制将状态从根组件“注入”到每一个子组件中（需调用 Vue.use(Vuex)）：
> 在根实例中注册 store 选项，该 store 实例会注入到根组件下的所有子组件中，且子组件能通过 this.\$store 访问到。

```js
const app = new Vue({
  el: "#app",
  // 把 store 对象提供给 “store” 选项，这可以把 store 的实例注入所有的子组件
  store,
  components: { Counter },
  template: `
    <div class="app">
      <counter></counter>
    </div>
  `
});

const Counter = {
  template: `<div>{{ count }}</div>`,
  computed: {
    count() {
      return this.$store.state.count;
    }
  }
};
```

### **mapState 辅助函数**

> 当一个组件需要获取多个状态时候，将这些状态都声明为计算属性会有些重复和冗余。为了解决这个问题，我们可以使用 mapState 辅助函数帮助我们生成计算属性

```js
// 在单独构建的版本中辅助函数为 Vuex.mapState
import { mapState } from 'vuex'

export default {
  // ...
  computed: mapState({
    // 箭头函数可使代码更简练
    count: state => state.count,

    // 传字符串参数 'count' 等同于 `state => state.count`
    countAlias: 'count',

    // 为了能够使用 `this` 获取局部状态，必须使用常规函数
    countPlusLocalState (state) {
      return state.count + this.localCount
    }
  })
}

// 当映射的计算属性的名称与 state 的子节点名称相同时，我们也可以给 mapState 传一个字符串数组。
computed: mapState([
  // 映射 this.count 为 store.state.count
  'count'
])

// 对象展开运算符
computed: {
  localComputed () { /* ... */ },
  // 使用对象展开运算符将此对象混入到外部对象中
  ...mapState({
    // ...
  })
}
```

> 使用 Vuex 并不意味着你需要将所有的状态放入 Vuex。虽然将所有的状态放到 Vuex 会使状态变化更显式和易调试，但也会使代码变得冗长和不直观。如果有些状态严格属于单个组件，最好还是作为组件的局部状态

---

## Getter

### **为什么要 Getter**

> Vuex 允许我们在 store 中定义“getter”（可以认为是 store 的计算属性）。就像计算属性一样，getter 的返回值会根据它的依赖被缓存起来，且只有当它的依赖值发生了改变才会被重新计算

```js
const store = new Vuex.Store({
  state: {
    todos: [
      { id: 1, text: "...", done: true },
      { id: 2, text: "...", done: false }
    ]
  },

  getters: {
    // Getter 接受 state 作为其第一个参数
    doneTodos: state => {
      return state.todos.filter(todo => todo.done);
    },

    // Getter 也可以接受其他 getter 作为第二个参数
    doneTodosCount: (state, getters) => {
      return getters.doneTodos.length;
    },

    //  getter 返回一个函数
    getTodoById: state => id => {
      return state.todos.find(todo => todo.id === id);
    }
  }
});
```

<br>

### **访问 Getter**

#### 通过属性访问

```js
store.getters.doneTodos; // -> [{ id: 1, text: '...', done: true }]
```

> 注意，getter 在通过属性访问时是作为 Vue 的响应式系统的一部分缓存其中的

<br>

#### **通过方法访问**

> 通过让 getter 返回一个函数，来实现给 getter 传参。在你对 store 里的数组进行查询时非常有用。

```js
store.getters.getTodoById(2); // -> { id: 2, text: '...', done: false }
```

> 注意，getter 在通过方法访问时，每次都会去进行调用，而不会缓存结果。

<br>

#### **mapGetters 辅助函数**

```js
// mapGetters 辅助函数仅仅是将 store 中的 getter 映射到局部计算属性
import { mapGetters } from "vuex";

export default {
  // ...
  computed: {
    // 使用对象展开运算符将 getter 混入 computed 对象中
    ...mapGetters([
      "doneTodosCount",
      "anotherGetter"
      // ...
    ])
  }
};

// 如果你想将一个 getter 属性另取一个名字，使用对象形式
mapGetters({
  // 把 `this.doneCount` 映射为 `this.$store.getters.doneTodosCount`
  doneCount: "doneTodosCount"
});
```

<br>

---

## Mutation

### **why mutation?**

> 更改 Vuex 的 store 中的状态的唯一方法是提交 mutation。Vuex 中的 mutation 非常类似于事件：每个 mutation 都有一个字符串的 事件类型 (type) 和 一个 回调函数 (handler)。这个回调函数就是我们实际进行状态更改的地方，并且它会接受 state 作为第一个参数

```js
const store = new Vuex.Store({
  state: {
    count: 1
  },
  mutations: {
    increment(state) {
      // 变更状态
      state.count++;
    }
  }
});
```

> 你不能直接调用一个 mutation handler。这个选项更像是事件注册：“当触发一个类型为 increment 的 mutation 时，调用此函数。”要唤醒一个 mutation handler，你需要以相应的 type 调用 store.commit 方法：

```js
store.commit("increment");
```

<br>

### **提交载荷**

```js
// 可以向 store.commit 传入额外的参数，即 mutation 的 载荷（payload）
mutations: {
  increment (state, n) {
    state.count += n
  }
}

store.commit('increment', 10)

// 大多数情况下，载荷应该是一个对象，这样可以包含多个字段并且记录的 mutation 会更易读
mutations: {
  increment (state, payload) {
    state.count += payload.amount
  }
}

store.commit('increment', {
  amount: 10
})
```

_对象风格提交_

```js
// 提交 mutation 的另一种方式是直接使用包含 type 属性的对象
store.commit({
  type: 'increment',
  amount: 10
})

// 当使用对象风格的提交方式，整个对象都作为载荷传给 mutation 函数，因此 handler 保持不变
mutations: {
  increment (state, payload) {
    state.count += payload.amount
  }
}
```

<br>

### **Mutation 需遵守 Vue 的响应规则**


> 既然 Vuex 的 store 中的状态是响应式的，那么当我们变更状态时，监视状态的 Vue 组件也会自动更新。这也意味着 Vuex 中的 mutation 也需要与使用 Vue 一样遵守一些注意事项

- 最好提前在你的 store 中初始化好所有所需属性
- 在对象上添加新属性
  - 使用 `Vue.set(obj, 'newProp', 123)`
  - 使用 `state.obj = { ...state.obj, newProp: 123 }`

<br>

### **使用常量替代 Mutation 事件类型**

> 使用常量替代 mutation 事件类型在各种 Flux 实现中是很常见的模式。这样可以使 linter 之类的工具发挥作用，同时把这些常量放在单独的文件中可以让你的代码合作者对整个 app 包含的 mutation 一目了然
> 用不用常量取决于你——在需要多人协作的大型项目中，这会很有帮助

```js
// mutation-types.js
export const SOME_MUTATION = 'SOME_MUTATION'

// store.js
import Vuex from 'vuex'
import { SOME_MUTATION } from './mutation-types'

const store = new Vuex.Store({
  state: { ... },
  mutations: {
    // 我们可以使用 ES2015 风格的计算属性命名功能来使用一个常量作为函数名
    [SOME_MUTATION] (state) {
      // mutate state
    }
  }
})
```

<br>

### **Mutation 必须是同步函数**

> 重要的原则就是要记住 mutation 必须是同步函数

```js
mutations: {
  someMutation (state) {
    api.callAsyncMethod(() => {
      state.count++
    })
  }
}
```

> 现在想象，我们正在 debug 一个 app 并且观察 devtool 中的 mutation 日志。每一条 mutation 被记录，devtools 都需要捕捉到前一状态和后一状态的快照。然而，在上面的例子中 mutation 中的异步函数中的回调让这不可能完成：因为当 mutation 触发的时候，回调函数还没有被调用，devtools 不知道什么时候回调函数实际上被调用——实质上任何在回调函数中进行的状态的改变都是不可追踪的

<br>

### **在组件中提交 Mutation**

> 可以在组件中使用 this.\$store.commit('xxx') 提交 mutation，或者使用 mapMutations 辅助函数将组件中的 methods 映射为 store.commit 调用（需要在根节点注入 store）

```js
import { mapMutations } from "vuex";

export default {
  // ...
  methods: {
    ...mapMutations([
      "increment", // 将 `this.increment()` 映射为 `this.$store.commit('increment')`

      // `mapMutations` 也支持载荷：
      "incrementBy" // 将 `this.incrementBy(amount)` 映射为 `this.$store.commit('incrementBy', amount)`
    ]),
    ...mapMutations({
      add: "increment" // 将 `this.add()` 映射为 `this.$store.commit('increment')`
    })
  }
};
```

<br>

---

## Action

### **mutation 与 action 的区别**

> mutation 都是同步事务
> Action 提交的是 mutation，而不是直接变更状态
> Action 可以包含任意异步操作

<br>

### **注册 action**

> Action 函数接受一个**与 store 实例具有相同方法和属性的 context 对象**，因此你可以调用 context.commit 提交一个 mutation，或者通过 context.state 和 context.getters 来获取 state 和 getters。当我们在之后介绍到 Modules 时，你就知道 **context 对象为什么不是 store 实例本身了**。

```js
const store = new Vuex.Store({
  state: {
    count: 0
  },
  mutations: {
    increment(state) {
      state.count++;
    }
  },
  actions: {
    increment(context) {
      context.commit("increment");
    }
  }
});
```

> 我们会经常用到 ES2015 的 参数解构 来简化代码（特别是我们需要调用 commit 很多次的时候）

```js
actions: {
  increment ({ commit }) {
    commit('increment')
  }
}
```

<br>


### **分发 action**

> Action 通过 store.dispatch 方法触发

`store.dispatch('increment')`

> 可以在 action 内部执行异步操作

```js
actions: {
  incrementAsync ({ commit }) {
    setTimeout(() => {
      commit('increment')
    }, 1000)
  }
}

// 以载荷形式分发
store.dispatch('incrementAsync', {
  amount: 10
})

// 以对象形式分发
store.dispatch({
  type: 'incrementAsync',
  amount: 10
})

// 涉及到调用异步 API 和分发多重 mutation
actions: {
  checkout ({ commit, state }, products) {
    // 把当前购物车的物品备份起来
    const savedCartItems = [...state.cart.added]
    // 发出结账请求，然后乐观地清空购物车
    commit(types.CHECKOUT_REQUEST)
    // 购物 API 接受一个成功回调和一个失败回调
    shop.buyProducts(
      products,
      // 成功操作
      () => commit(types.CHECKOUT_SUCCESS),
      // 失败操作
      () => commit(types.CHECKOUT_FAILURE, savedCartItems)
    )
  }
}
```

<br>

### **在组件中分发 Action**

> 在组件中使用 this.\$store.dispatch('xxx') 分发 action，或者使用 mapActions 辅助函数将组件的 methods 映射为 store.dispatch 调用（需要先在根节点注入 store）

```js
import { mapActions } from "vuex";

export default {
  // ...
  methods: {
    ...mapActions([
      "increment", // 将 `this.increment()` 映射为 `this.$store.dispatch('increment')`

      // `mapActions` 也支持载荷：
      "incrementBy" // 将 `this.incrementBy(amount)` 映射为 `this.$store.dispatch('incrementBy', amount)`
    ]),
    ...mapActions({
      add: "increment" // 将 `this.add()` 映射为 `this.$store.dispatch('increment')`
    })
  }
};
```

<br>

### **组合 Action**

> Action 通常是异步的，那么如何知道 action 什么时候结束呢？更重要的是，我们如何才能组合多个 action，以处理更加复杂的异步流程？
> 首先，你需要明白 store.dispatch 可以处理 action 返回的 Promise，并且 store.dispatch 仍旧返回 Promise

```js
actions: {
  actionA ({ commit }) {
    return new Promise((resolve, reject) => {
      setTimeout(() => {
        commit('someMutation')
        resolve()
      }, 1000)
    })
  }
}

// 串行异步
store.dispatch('actionA').then(() => {
  // ...
})

// 另外一个 action 中也可以
actions: {
  // ...
  actionB ({ dispatch, commit }) {
    return dispatch('actionA').then(() => {
      commit('someOtherMutation')
    })
  }
}
```

> 如果我们利用 async / await，我们可以如下组合 action

```js
// 假设 getData() 和 getOtherData() 返回的是 Promise

actions: {
  async actionA ({ commit }) {
    commit('gotData', await getData())
  },
  async actionB ({ dispatch, commit }) {
    await dispatch('actionA') // 等待 actionA 完成
    commit('gotOtherData', await getOtherData())
  }
}
```

> 一个 store.dispatch 在不同模块中可以触发多个 action 函数。在这种情况下，只有当所有触发函数完成后，返回的 Promise 才会执行

### async / await

#### async

>Async functions async关键字被放置在一个函数前
函数前面的async一词意味着一个简单的事情：这个函数总是返回一个promise，如果代码中有return <非promise>语句，JavaScript会自动把返回的这个value值包装成promise的resolved值

```js
// 返回resolved值为1的promis
async function f() {
    return 1
}

async function f() {
    return 1
}
f().then(alert) // 1


// 可以显式的返回一个promise
async function f() {
    return Promise.resolve(1)
}
f().then(alert) // 1
```

>async确保了函数返回一个promise，即使其中包含非promise。够简单了吧？但是不仅仅只是如此，还有另一个关键词await，只能在async函数里使用


#### await

> 关键词await可以让JavaScript进行等待，直到一个promise执行并返回它的结果，JavaScript才会继续往下执行

```js
// 只能在async函数内部使用
let value = await promise

// 一个promise在1s之后resolve的例子
// 函数执行到（*）行会‘暂停’，当promise处理完成后重新恢复运行， resolve的值成了最终的result，所以上面的代码会在1s后输出'done!'
async function f() {
    let promise = new Promise((resolve, reject) => {
        setTimeout(() => resolve('done!'), 1000)
    })
    let result = await promise // 直到promise返回一个resolve值（*）
    alert(result) // 'done!' 
}
f()
```

>我们强调一下：await字面上使得JavaScript等待，直到promise处理完成，
然后将结果继续下去。这并不会花费任何的cpu资源，因为引擎能够同时做其他工作：执行其他脚本，处理事件等等。
>>这只是一个更优雅的得到promise值的语句，它比promise更加容易阅读和书写。


#### 总结

- async

   - 使函数总是返回一个promise
   - 允许在这其中使用await

- await

   - promise前面的await关键字能够使JavaScript等待，直到promise处理结束
   - 如果它是一个错误，异常就产生了，就像在那个地方调用了throw error一样
   - 否则，它会返回一个结果，我们可以将它分配给一个值

<br>

---

## Module

### **why**

> 由于使用单一状态树，应用的所有状态会集中到一个比较大的对象。当应用变得非常复杂时，store 对象就有可能变得相当臃肿

> Vuex 允许我们将 store 分割成模块（module）。每个模块拥有自己的

```js
const moduleA = {
  state: { ... },
  mutations: { ... },
  actions: { ... },
  getters: { ... }
}

const moduleB = {
  state: { ... },
  mutations: { ... },
  actions: { ... }
}

const store = new Vuex.Store({
  modules: {
    a: moduleA,
    b: moduleB
  }
})

store.state.a // -> moduleA 的状态
store.state.b // -> moduleB 的状态
```

<br>

### **模块的局部状态**

> 对于模块内部的 mutation 和 getter，接收的第一个参数是模块的局部状态对象

```js
const moduleA = {
  state: { count: 0 },
  mutations: {
    increment(state) {
      // 这里的 `state` 对象是模块的局部状态
      state.count++;
    }
  },

  getters: {
    doubleCount(state) {
      return state.count * 2;
    }
  }
};
```

> 对于模块内部的 action，局部状态通过 context.state 暴露出来，根节点状态则为 context.rootState

```js
const moduleA = {
  // ...
  actions: {
    incrementIfOddOnRootSum({ state, commit, rootState }) {
      if ((state.count + rootState.count) % 2 === 1) {
        commit("increment");
      }
    }
  }
};
```

> 模块内部的 getter，根节点状态会作为第三个参数暴露出来

```js
const moduleA = {
  // ...
  getters: {
    sumWithRootCount(state, getters, rootState) {
      return state.count + rootState.count;
    }
  }
};
```

<br>

### **命名空间**

> 默认情况下，模块内部的 action、mutation 和 getter 是注册在全局命名空间的——这样使得多个模块能够对同一 mutation 或 action 作出响应

> 如果希望你的模块具有更高的封装度和复用性，你可以通过添加 namespaced: true 的方式使其成为带命名空间的模块。当模块被注册后，它的所有 getter、action 及 mutation 都会自动根据模块注册的路径调整命名

```js
const store = new Vuex.Store({
  modules: {
    account: {
      namespaced: true,

      // 模块内容（module assets）
      state: { ... }, // 模块内的状态已经是嵌套的了，使用 `namespaced` 属性不会对其产生影响
      getters: {
        isAdmin () { ... } // -> getters['account/isAdmin']
      },
      actions: {
        login () { ... } // -> dispatch('account/login')
      },
      mutations: {
        login () { ... } // -> commit('account/login')
      },

      // 嵌套模块
      modules: {
        // 继承父模块的命名空间
        myPage: {
          state: { ... },
          getters: {
            profile () { ... } // -> getters['account/profile']
          }
        },

        // 进一步嵌套命名空间
        posts: {
          namespaced: true,

          state: { ... },
          getters: {
            popular () { ... } // -> getters['account/posts/popular']
          }
        }
      }
    }
  }
})
```

> 启用了命名空间的 getter 和 action 会收到局部化的 getter，dispatch 和 commit。换言之，你在使用模块内容（module assets）时不需要在同一模块内额外添加空间名前缀。更改 namespaced 属性后不需要修改模块内的代码。

<br>

### **在带命名空间的模块内访问全局内容（Global Assets）**

> 如果你希望使用全局 state 和 getter，rootState 和 rootGetter 会作为第三和第四参数传入 getter，也会通过 context 对象的属性传入 action。
> 若需要在全局命名空间内分发 action 或提交 mutation，将 { root: true } 作为第三参数传给 dispatch 或 commit 即可。

```js
modules: {
  foo: {
    namespaced: true,

    getters: {
      // 在这个模块的 getter 中，`getters` 被局部化了
      // 你可以使用 getter 的第四个参数来调用 `rootGetters`
      someGetter (state, getters, rootState, rootGetters) {
        getters.someOtherGetter // -> 'foo/someOtherGetter'
        rootGetters.someOtherGetter // -> 'someOtherGetter'
      },
      someOtherGetter: state => { ... }
    },

    actions: {
      // 在这个模块中， dispatch 和 commit 也被局部化了
      // 他们可以接受 `root` 属性以访问根 dispatch 或 commit
      someAction ({ dispatch, commit, getters, rootGetters }) {
        getters.someGetter // -> 'foo/someGetter'
        rootGetters.someGetter // -> 'someGetter'

        dispatch('someOtherAction') // -> 'foo/someOtherAction'
        dispatch('someOtherAction', null, { root: true }) // -> 'someOtherAction'

        commit('someMutation') // -> 'foo/someMutation'
        commit('someMutation', null, { root: true }) // -> 'someMutation'
      },
      someOtherAction (ctx, payload) { ... }
    }
  }
}
```

<br>

### **带命名空间的模块注册全局 action**

> 若需要在带命名空间的模块注册全局 action，你可添加 root: true，并将这个 action 的定义放在函数 handler 中

```js
{
  actions: {
    someOtherAction ({dispatch}) {
      dispatch('someAction')
    }
  },
  modules: {
    foo: {
      namespaced: true,

      actions: {
        someAction: {
          root: true,
          handler (namespacedContext, payload) { ... } // -> 'someAction'
        }
      }
    }
  }
}
```

<br>

### **带命名空间的绑定函数**

> 当使用 mapState, mapGetters, mapActions 和 mapMutations 这些函数来绑定带命名空间的模块时，写起来可能比较繁琐

```js
computed: {
  ...mapState({
    a: state => state.some.nested.module.a,
    b: state => state.some.nested.module.b
  })
},
methods: {
  ...mapActions([
    'some/nested/module/foo', // -> this['some/nested/module/foo']()
    'some/nested/module/bar' // -> this['some/nested/module/bar']()
  ])
}
```

> 对于这种情况，你可以将模块的空间名称字符串作为第一个参数传递给上述函数，这样所有绑定都会自动将该模块作为上下文。于是上面的例子可以简化为

```js
computed: {
  ...mapState('some/nested/module', {
    a: state => state.a,
    b: state => state.b
  })
},
methods: {
  ...mapActions('some/nested/module', [
    'foo', // -> this.foo()
    'bar' // -> this.bar()
  ])
}
```

> 可以通过使用 createNamespacedHelpers 创建基于某个命名空间辅助函数。它返回一个对象，对象里有新的绑定在给定命名空间值上的组件绑定辅助函数

```js
import { createNamespacedHelpers } from "vuex";

const { mapState, mapActions } = createNamespacedHelpers("some/nested/module");

export default {
  computed: {
    // 在 `some/nested/module` 中查找
    ...mapState({
      a: state => state.a,
      b: state => state.b
    })
  },
  methods: {
    // 在 `some/nested/module` 中查找
    ...mapActions(["foo", "bar"])
  }
};
```

<br>

### **插件开发者的注意事项**

> 如果你开发的插件（Plugin）提供了模块并允许用户将其添加到 Vuex store，可能需要考虑模块的空间名称问题。对于这种情况，你可以通过插件的参数对象来允许用户指定空间名称

```js
// 通过插件的参数对象得到空间名称
// 然后返回 Vuex 插件函数
export function createPlugin(options = {}) {
  return function(store) {
    // 把空间名字添加到插件模块的类型（type）中去
    const namespace = options.namespace || "";
    store.dispatch(namespace + "pluginAction");
  };
}
```

<br>

### **模块动态注册**

> 在 store 创建之后，你可以使用 store.registerModule 方法注册模块

```js
// 注册模块 `myModule`
store.registerModule("myModule", {
  // ...
});
// 注册嵌套模块 `nested/myModule`
store.registerModule(["nested", "myModule"], {
  // ...
});
```

> 之后就可以通过 store.state.myModule 和 store.state.nested.myModule 访问模块的状态。

> 之后就可以通过 store.state.myModule 和 store.state.nested.myModule 访问模块的状态。
> 模块动态注册功能使得其他 Vue 插件可以通过在 store 中附加新模块的方式来使用 Vuex 管理状态。

> 例如，vuex-router-sync 插件就是通过动态注册模块将 vue-router 和 vuex 结合在一起，实现应用的路由状态管理。

> 你也可以使用 store.unregisterModule(moduleName) 来动态卸载模块。注意，你不能使用此方法卸载静态模块（即创建 store 时声明的模块）

<br>

_保留 state_

> 在注册一个新 module 时，你很有可能想保留过去的 state，例如从一个服务端渲染的应用保留 state。你可以通过 preserveState 选项将其归档：store.registerModule('a', module, { preserveState: true })。

> 当你设置 preserveState: true 时，该模块会被注册，action、mutation 和 getter 会被添加到 store 中，但是 state 不会。这里假设 store 的 state 已经包含了这个 module 的 state 并且你不希望将其覆写

<br>

### **模块重用**

> 有时我们可能需要创建一个模块的多个实例
>
> > 创建多个 store，他们公用同一个模块 (例如当 runInNewContext 选项是 false 或 'once' 时，为了在服务端渲染中避免有状态的单例)
> > 在一个 store 中多次注册同一个模块

> 如果我们使用一个纯对象来声明模块的状态，那么这个状态对象会通过引用被共享，导致状态对象被修改时 store 或模块间数据互相污染的问题。
>
> > 实际上这和 Vue 组件内的 data 是同样的问题。因此解决办法也是相同的——使用一个函数来声明模块状态（仅 2.3.0+ 支持）

```js
const MyReusableModule = {
  state() {
    return {
      foo: "bar"
    };
  }
  // mutation, action 和 getter 等等...
};
```

<br>

---

## 项目结构

- Vuex 并不限制你的代码结构。但是，它规定了一些需要遵守的规则：

  - 应用层级的状态应该集中到单个 store 对象中。

  - 提交 mutation 是更改状态的唯一方法，并且这个过程是同步的。

  - 异步逻辑都应该封装到 action 里面。

> 只要你遵守以上规则，如何组织代码随你便。如果你的 store 文件太大，只需将 action、mutation 和 getter 分割到单独的文件

├── index.html
├── main.js
├── api
│ └── ... # 抽取出 API 请求
├── components
│ ├── App.vue
│ └── ...
└── store
├── index.js # 我们组装模块并导出 store 的地方
├── actions.js # 根级别的 action
├── mutations.js # 根级别的 mutation
└── modules
├── cart.js # 购物车模块
└── products.js # 产品模块

<br>

---

## 热重载

> 使用 webpack 的 Hot Module Replacement API，Vuex 支持在开发过程中热重载 mutation、module、action 和 getter。你也可以在 Browserify 中使用 browserify-hmr 插件

> 对于 mutation 和模块，你需要使用 store.hotUpdate() 方法

```js
// store.js
import Vue from 'vue'
import Vuex from 'vuex'
import mutations from './mutations'
import moduleA from './modules/a'

Vue.use(Vuex)

const state = { ... }

const store = new Vuex.Store({
  state,
  mutations,
  modules: {
    a: moduleA
  }
})

if (module.hot) {
  // 使 action 和 mutation 成为可热重载模块
  module.hot.accept(['./mutations', './modules/a'], () => {
    // 获取更新后的模块
    // 因为 babel 6 的模块编译格式问题，这里需要加上 `.default`
    const newMutations = require('./mutations').default
    const newModuleA = require('./modules/a').default
    // 加载新模块
    store.hotUpdate({
      mutations: newMutations,
      modules: {
        a: newModuleA
      }
    })
  })
}
```
