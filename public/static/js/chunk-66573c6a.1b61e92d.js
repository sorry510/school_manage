(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["chunk-66573c6a"],{"08fd":function(e,t,n){},"1a6c":function(e,t,n){"use strict";n("08fd")},2017:function(e,t,n){"use strict";n("cafe")},"3e10":function(e,t,n){"use strict";function o(e,t,n,o){var i=void 0!==window.screenLeft?window.screenLeft:screen.left,s=void 0!==window.screenTop?window.screenTop:screen.top,a=window.innerWidth?window.innerWidth:document.documentElement.clientWidth?document.documentElement.clientWidth:screen.width,r=window.innerHeight?window.innerHeight:document.documentElement.clientHeight?document.documentElement.clientHeight:screen.height,l=a/2-n/2+i,c=r/2-o/2+s,u=window.open(e,t,"toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=yes, copyhistory=no, width="+n+", height="+o+", top="+c+", left="+l);window.focus&&u.focus()}n.d(t,"a",(function(){return o}))},9378:function(e,t,n){},"9ed6":function(e,t,n){"use strict";n.r(t);var o=function(){var e=this,t=e.$createElement,n=e._self._c||t;return n("div",{staticClass:"login-container"},[n("el-form",{ref:"loginForm",staticClass:"login-form",attrs:{model:e.loginForm,rules:e.loginRules,autocomplete:"on","label-position":"left"}},[n("div",{staticClass:"title-container"},[n("h3",{staticClass:"title"},[e._v("教务管理系统")])]),n("el-form-item",{attrs:{prop:"username"}},[n("span",{staticClass:"svg-container"},[n("svg-icon",{attrs:{"icon-class":"user"}})],1),n("el-input",{ref:"username",attrs:{placeholder:"用户名",name:"username",type:"text",tabindex:"1",autocomplete:"on"},model:{value:e.loginForm.username,callback:function(t){e.$set(e.loginForm,"username",t)},expression:"loginForm.username"}})],1),n("el-tooltip",{attrs:{content:"Caps lock is On",placement:"right",manual:""},model:{value:e.capsTooltip,callback:function(t){e.capsTooltip=t},expression:"capsTooltip"}},[n("el-form-item",{attrs:{prop:"password"}},[n("span",{staticClass:"svg-container"},[n("svg-icon",{attrs:{"icon-class":"password"}})],1),n("el-input",{key:e.passwordType,ref:"password",attrs:{type:e.passwordType,placeholder:"密码",name:"password",tabindex:"2",autocomplete:"on"},on:{blur:function(t){e.capsTooltip=!1}},nativeOn:{keyup:[function(t){return e.checkCapslock(t)},function(t){return!t.type.indexOf("key")&&e._k(t.keyCode,"enter",13,t.key,"Enter")?null:e.handleLogin(t)}]},model:{value:e.loginForm.password,callback:function(t){e.$set(e.loginForm,"password",t)},expression:"loginForm.password"}}),n("span",{staticClass:"show-pwd",on:{click:e.showPwd}},[n("svg-icon",{attrs:{"icon-class":"password"===e.passwordType?"eye":"eye-open"}})],1)],1)],1),n("el-form-item",{attrs:{prop:"type"}},[n("span",{staticClass:"svg-container"},[n("i",{staticClass:"el-icon-discount"})]),n("el-radio-group",{staticStyle:{"padding-left":"12px"},model:{value:e.loginForm.type,callback:function(t){e.$set(e.loginForm,"type",t)},expression:"loginForm.type"}},[n("el-radio",{attrs:{label:"teacher"}},[e._v("教师")]),n("el-radio",{attrs:{label:"student"}},[e._v("学生")])],1)],1),n("el-button",{staticStyle:{width:"100%","margin-bottom":"30px"},attrs:{loading:e.loading,type:"success"},nativeOn:{click:function(t){return t.preventDefault(),e.handleLogin(t)}}},[e._v("登录")]),n("div",{staticStyle:{position:"relative","margin-top":"30px"}},[n("el-button",{staticClass:"register-button",attrs:{type:"text"}},[n("router-link",{attrs:{to:"/register"}},[e._v(" 教师注册 ")])],1),n("el-button",{staticClass:"thirdparty-button",attrs:{type:"primary"},on:{click:function(t){e.showDialog=!0}}},[e._v(" 第三方登录 ")])],1)],1),n("el-dialog",{attrs:{title:"第三方登录",visible:e.showDialog,"custom-class":"login-dialog"},on:{"update:visible":function(t){e.showDialog=t}}},[n("social-sign")],1)],1)},i=[],s=(n("d3b7"),n("b64b"),function(){var e=this,t=e.$createElement,n=e._self._c||t;return n("div",{staticClass:"social-signup-container"},[e.callback?n("div",[n("el-row",[n("el-col",{attrs:{span:24}},[n("el-avatar",{staticStyle:{"margin-left":"15px"},attrs:{size:"middle",src:e.line.avatar}})],1),n("el-col",{attrs:{span:24}},[n("el-select",{attrs:{placeholder:"请选择账号"},on:{change:e.handleLogin},model:{value:e.value,callback:function(t){e.value=t},expression:"value"}},[e._l(e.teachers,(function(e){return n("el-option",{key:e.line_user_id+"-"+e.relation_id+"-"+e.type,attrs:{label:e.name+"(教师)",value:e.line_user_id+"-"+e.relation_id+"-"+e.type}})})),e._l(e.students,(function(e){return n("el-option",{key:e.line_user_id+"-"+e.relation_id+"-"+e.type,attrs:{label:e.name+"(学生)",value:e.line_user_id+"-"+e.relation_id+"-"+e.type}})}))],2)],1)],1)],1):n("div",{staticClass:"sign-btn",on:{click:function(t){return e.lineHandleClick("Line")}}},[e._m(0),e._v(" Line ")])])}),a=[function(){var e=this,t=e.$createElement,n=e._self._c||t;return n("span",{staticClass:"wx-svg-container"},[n("i",{staticClass:"el-icon-chat-dot-round"})])}],r=n("3835"),l=(n("ac1f"),n("1276"),n("3e10")),c={name:"SocialSignIn",data:function(){return{callback:!1,line:{id:"",name:"",avatar:"",email:""},value:"",teachers:[],students:[]}},methods:{lineHandleClick:function(e){Object(l["a"])("".concat("","/login/line"),e,540,540),window.addEventListener("message",this.handleMessage.bind(this),!1)},handleMessage:function(e){if("string"===typeof e.data&&!this.callback)try{var t=JSON.parse(e.data),n=t.line,o=t.relations,i=o.teachers,s=o.students;this.line=n,this.teachers=i,this.students=s,this.callback=!0}catch(e){console.log("error data: ",e.data)}console.log(e)},handleLogin:function(e){var t=this;this.value=e;var n=e.split("-"),o=Object(r["a"])(n,3),i=o[0],s=o[1],a=o[2];console.log(e),this.$store.dispatch("user/lineLogin",{line_id:i,relation_id:s,type:a}).then((function(){t.$message({message:"登录成功",type:"success"}),t.$router.push({path:t.redirect||"/",query:t.otherQuery})})).catch((function(){}))}}},u=c,d=(n("1a6c"),n("2877")),p=Object(d["a"])(u,s,a,!1,null,"e5a974d4",null),h=p.exports,f={name:"Login",components:{SocialSign:h},data:function(){var e=function(e,t,n){t.length<1?n(new Error("请输入用户名")):n()},t=function(e,t,n){t.length<3?n(new Error("密码长度不能小于3")):n()};return{loginForm:{username:"",password:"",type:"teacher"},loginRules:{username:[{required:!0,trigger:"blur",validator:e}],password:[{required:!0,trigger:"blur",validator:t}]},passwordType:"password",capsTooltip:!1,loading:!1,showDialog:!1,redirect:void 0,otherQuery:{}}},watch:{$route:{handler:function(e){var t=e.query;t&&(this.redirect=t.redirect,this.otherQuery=this.getOtherQuery(t))},immediate:!0}},created:function(){this.$socket.close()},mounted:function(){""===this.loginForm.username?this.$refs.username.focus():""===this.loginForm.password&&this.$refs.password.focus()},destroyed:function(){},methods:{checkCapslock:function(e){var t=e.key;this.capsTooltip=t&&1===t.length&&t>="A"&&t<="Z"},showPwd:function(){var e=this;"password"===this.passwordType?this.passwordType="":this.passwordType="password",this.$nextTick((function(){e.$refs.password.focus()}))},handleLogin:function(){var e=this;this.$refs.loginForm.validate((function(t){if(!t)return console.log("error submit!!"),!1;e.loading=!0,e.$store.dispatch("user/login",e.loginForm).then((function(){e.$router.push({path:e.redirect||"/",query:e.otherQuery}),e.loading=!1})).catch((function(){e.loading=!1}))}))},getOtherQuery:function(e){return Object.keys(e).reduce((function(t,n){return"redirect"!==n&&(t[n]=e[n]),t}),{})}}},g=f,m=(n("2017"),n("faa1"),Object(d["a"])(g,o,i,!1,null,"7f7cd383",null));t["default"]=m.exports},cafe:function(e,t,n){},faa1:function(e,t,n){"use strict";n("9378")}}]);