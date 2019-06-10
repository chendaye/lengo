<template>
  <body>
    <div id="trial">
      <svg
        version="1.2"
        height="100"
        width="300"
        xmlns="http://www.w3.org/2000/svg"
        viewport="0 0 60 60"
        xmlns:xlink="http://www.w3.org/1999/xlink"
      >
        <path
          stroke="rgba(15,0,0,0.1)"
          fill="none"
          stroke-width="1"
          stroke-linejoin="round"
          d="
            M0,90L250,90Q257,60 262,87T267,95 270,88 273,92t6,35 7,-60T290,127 297,107s2,-11 10,-10 1,1 8,-10T319,95c6,4 8,-6 10,-17s2,10 9,11h210"
        />

        <path
          id="pulsar"
          stroke="rgba(0,155,155,1)"
          fill="none"
          stroke-width="1"
          stroke-linejoin="round"
          d="
            M0,90L250,90Q257,60 262,87T267,95 270,88 273,92t6,35 7,-60T290,127 297,107s2,-11 10,-10 1,1 8,-10T319,95c6,4 8,-6 10,-17s2,10 9,11h210"
        />
      </svg>
    </div>
  </body>
</template>
<script>
export default {
  name: 'MeteorBg'
}
</script>

<style>
body {
  background: #4a6d6d;
  color: #b15858;
  font-family: sans-serif;
}
hr {
  width: 100%;
  height: 1px;
  color: #fff;
}

#trial {
  width: 100%;
  max-width: 700px;
  margin: 0 auto;
  padding: 0;
  position: relative;
}

svg {
  width: 100%;
  height: 100%;
}

#pulsar {
  stroke-dasharray: 281;
  -webkit-animation: dash 2.5s infinite linear forwards;
}

#jugular {
  stroke-dasharray: 497;
  -webkit-animation: dash 1.4s infinite ease forwards;
}

#bleed {
  stroke-dasharray: 437;
  -webkit-animation: dash 1.2s infinite ease-out;
}
#flat {
  stroke-dasharray: 814;
  -webkit-animation: dash 10s infinite linear;
}
/*Animation is the same for all 3 SVG's*/
@-webkit-keyframes dash {
  from {
    stroke-dashoffset: 814;
  }
  to {
    stroke-dashoffset: -814;
  }
}
</style>
