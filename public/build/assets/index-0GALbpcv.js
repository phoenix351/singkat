import{r as n,m as Y}from"./app-D_OB5lB0.js";import{e as Z,a as ee,f as N,k as T,_ as re,d as G,g as te,m as ae,r as M,J as ne,u as F,C as W,D as oe,n as H}from"./index-CbOp89og.js";import{u as le}from"./zoom-jj1jlWM7.js";import{T as se,W as ie,o as ce}from"./button-DwurTV-5.js";import{F as de,u as q}from"./PurePanel-BBLJmiT4.js";var ue=["prefixCls","className","style","checked","disabled","defaultChecked","type","title","onChange"],be=n.forwardRef(function(e,r){var t=e.prefixCls,a=t===void 0?"rc-checkbox":t,i=e.className,I=e.style,g=e.checked,m=e.disabled,S=e.defaultChecked,C=S===void 0?!1:S,u=e.type,x=u===void 0?"checkbox":u,j=e.title,s=e.onChange,O=Z(e,ue),h=n.useRef(null),b=n.useRef(null),o=le(C,{value:g}),w=ee(o,2),P=w[0],y=w[1];n.useImperativeHandle(r,function(){return{focus:function(d){var p;(p=h.current)===null||p===void 0||p.focus(d)},blur:function(){var d;(d=h.current)===null||d===void 0||d.blur()},input:h.current,nativeElement:b.current}});var f=N(a,i,T(T({},"".concat(a,"-checked"),P),"".concat(a,"-disabled"),m)),c=function(d){m||("checked"in e||y(d.target.checked),s==null||s({target:G(G({},e),{},{type:x,checked:d.target.checked}),stopPropagation:function(){d.stopPropagation()},preventDefault:function(){d.preventDefault()},nativeEvent:d.nativeEvent}))};return n.createElement("span",{className:f,title:j,style:I,ref:b},n.createElement("input",re({},O,{className:"".concat(a,"-input"),ref:h,onChange:c,disabled:m,checked:!!P,type:x})),n.createElement("span",{className:"".concat(a,"-inner")}))});const fe=e=>{const{checkboxCls:r}=e,t=`${r}-wrapper`;return[{[`${r}-group`]:Object.assign(Object.assign({},M(e)),{display:"inline-flex",flexWrap:"wrap",columnGap:e.marginXS,[`> ${e.antCls}-row`]:{flex:1}}),[t]:Object.assign(Object.assign({},M(e)),{display:"inline-flex",alignItems:"baseline",cursor:"pointer","&:after":{display:"inline-block",width:0,overflow:"hidden",content:"'\\a0'"},[`& + ${t}`]:{marginInlineStart:0},[`&${t}-in-form-item`]:{'input[type="checkbox"]':{width:14,height:14}}}),[r]:Object.assign(Object.assign({},M(e)),{position:"relative",whiteSpace:"nowrap",lineHeight:1,cursor:"pointer",borderRadius:e.borderRadiusSM,alignSelf:"center",[`${r}-input`]:{position:"absolute",inset:0,zIndex:1,cursor:"pointer",opacity:0,margin:0,[`&:focus-visible + ${r}-inner`]:Object.assign({},ne(e))},[`${r}-inner`]:{boxSizing:"border-box",display:"block",width:e.checkboxSize,height:e.checkboxSize,direction:"ltr",backgroundColor:e.colorBgContainer,border:`${F(e.lineWidth)} ${e.lineType} ${e.colorBorder}`,borderRadius:e.borderRadiusSM,borderCollapse:"separate",transition:`all ${e.motionDurationSlow}`,"&:after":{boxSizing:"border-box",position:"absolute",top:"50%",insetInlineStart:"25%",display:"table",width:e.calc(e.checkboxSize).div(14).mul(5).equal(),height:e.calc(e.checkboxSize).div(14).mul(8).equal(),border:`${F(e.lineWidthBold)} solid ${e.colorWhite}`,borderTop:0,borderInlineStart:0,transform:"rotate(45deg) scale(0) translate(-50%,-50%)",opacity:0,content:'""',transition:`all ${e.motionDurationFast} ${e.motionEaseInBack}, opacity ${e.motionDurationFast}`}},"& + span":{paddingInlineStart:e.paddingXS,paddingInlineEnd:e.paddingXS}})},{[`
        ${t}:not(${t}-disabled),
        ${r}:not(${r}-disabled)
      `]:{[`&:hover ${r}-inner`]:{borderColor:e.colorPrimary}},[`${t}:not(${t}-disabled)`]:{[`&:hover ${r}-checked:not(${r}-disabled) ${r}-inner`]:{backgroundColor:e.colorPrimaryHover,borderColor:"transparent"},[`&:hover ${r}-checked:not(${r}-disabled):after`]:{borderColor:e.colorPrimaryHover}}},{[`${r}-checked`]:{[`${r}-inner`]:{backgroundColor:e.colorPrimary,borderColor:e.colorPrimary,"&:after":{opacity:1,transform:"rotate(45deg) scale(1) translate(-50%,-50%)",transition:`all ${e.motionDurationMid} ${e.motionEaseOutBack} ${e.motionDurationFast}`}}},[`
        ${t}-checked:not(${t}-disabled),
        ${r}-checked:not(${r}-disabled)
      `]:{[`&:hover ${r}-inner`]:{backgroundColor:e.colorPrimaryHover,borderColor:"transparent"}}},{[r]:{"&-indeterminate":{[`${r}-inner`]:{backgroundColor:e.colorBgContainer,borderColor:e.colorBorder,"&:after":{top:"50%",insetInlineStart:"50%",width:e.calc(e.fontSizeLG).div(2).equal(),height:e.calc(e.fontSizeLG).div(2).equal(),backgroundColor:e.colorPrimary,border:0,transform:"translate(-50%, -50%) scale(1)",opacity:1,content:'""'}}}}},{[`${t}-disabled`]:{cursor:"not-allowed"},[`${r}-disabled`]:{[`&, ${r}-input`]:{cursor:"not-allowed",pointerEvents:"none"},[`${r}-inner`]:{background:e.colorBgContainerDisabled,borderColor:e.colorBorder,"&:after":{borderColor:e.colorTextDisabled}},"&:after":{display:"none"},"& + span":{color:e.colorTextDisabled},[`&${r}-indeterminate ${r}-inner::after`]:{background:e.colorTextDisabled}}}]};function pe(e,r){const t=ae(r,{checkboxCls:`.${e}`,checkboxSize:r.controlInteractiveSize});return[fe(t)]}const A=te("Checkbox",(e,r)=>{let{prefixCls:t}=r;return[pe(t,e)]}),L=Y.createContext(null);var me=function(e,r){var t={};for(var a in e)Object.prototype.hasOwnProperty.call(e,a)&&r.indexOf(a)<0&&(t[a]=e[a]);if(e!=null&&typeof Object.getOwnPropertySymbols=="function")for(var i=0,a=Object.getOwnPropertySymbols(e);i<a.length;i++)r.indexOf(a[i])<0&&Object.prototype.propertyIsEnumerable.call(e,a[i])&&(t[a[i]]=e[a[i]]);return t};const he=(e,r)=>{var t;const{prefixCls:a,className:i,rootClassName:I,children:g,indeterminate:m=!1,style:S,onMouseEnter:C,onMouseLeave:u,skipGroup:x=!1,disabled:j}=e,s=me(e,["prefixCls","className","rootClassName","children","indeterminate","style","onMouseEnter","onMouseLeave","skipGroup","disabled"]),{getPrefixCls:O,direction:h,checkbox:b}=n.useContext(W),o=n.useContext(L),{isFormItemInput:w}=n.useContext(de),P=n.useContext(oe),y=(t=(o==null?void 0:o.disabled)||j)!==null&&t!==void 0?t:P,f=n.useRef(s.value);n.useEffect(()=>{o==null||o.registerValue(s.value)},[]),n.useEffect(()=>{if(!x)return s.value!==f.current&&(o==null||o.cancelValue(f.current),o==null||o.registerValue(s.value),f.current=s.value),()=>o==null?void 0:o.cancelValue(s.value)},[s.value]);const c=O("checkbox",a),v=q(c),[d,p,V]=A(c,v),$=Object.assign({},s);o&&!x&&($.onChange=function(){s.onChange&&s.onChange.apply(s,arguments),o.toggleOption&&o.toggleOption({label:g,value:s.value})},$.name=o.name,$.checked=o.value.includes(s.value));const k=N(`${c}-wrapper`,{[`${c}-rtl`]:h==="rtl",[`${c}-wrapper-checked`]:$.checked,[`${c}-wrapper-disabled`]:y,[`${c}-wrapper-in-form-item`]:w},b==null?void 0:b.className,i,I,V,v,p),R=N({[`${c}-indeterminate`]:m},se,p),D=m?"mixed":void 0;return d(n.createElement(ie,{component:"Checkbox",disabled:y},n.createElement("label",{className:k,style:Object.assign(Object.assign({},b==null?void 0:b.style),S),onMouseEnter:C,onMouseLeave:u},n.createElement(be,Object.assign({"aria-checked":D},$,{prefixCls:c,className:R,disabled:y,ref:r})),g!==void 0&&n.createElement("span",null,g))))},X=n.forwardRef(he);var ve=function(e,r){var t={};for(var a in e)Object.prototype.hasOwnProperty.call(e,a)&&r.indexOf(a)<0&&(t[a]=e[a]);if(e!=null&&typeof Object.getOwnPropertySymbols=="function")for(var i=0,a=Object.getOwnPropertySymbols(e);i<a.length;i++)r.indexOf(a[i])<0&&Object.prototype.propertyIsEnumerable.call(e,a[i])&&(t[a[i]]=e[a[i]]);return t};const ge=n.forwardRef((e,r)=>{const{defaultValue:t,children:a,options:i=[],prefixCls:I,className:g,rootClassName:m,style:S,onChange:C}=e,u=ve(e,["defaultValue","children","options","prefixCls","className","rootClassName","style","onChange"]),{getPrefixCls:x,direction:j}=n.useContext(W),[s,O]=n.useState(u.value||t||[]),[h,b]=n.useState([]);n.useEffect(()=>{"value"in u&&O(u.value||[])},[u.value]);const o=n.useMemo(()=>i.map(l=>typeof l=="string"||typeof l=="number"?{label:l,value:l}:l),[i]),w=l=>{b(E=>E.filter(_=>_!==l))},P=l=>{b(E=>[].concat(H(E),[l]))},y=l=>{const E=s.indexOf(l.value),_=H(s);E===-1?_.push(l.value):_.splice(E,1),"value"in u||O(_),C==null||C(_.filter(z=>h.includes(z)).sort((z,K)=>{const Q=o.findIndex(B=>B.value===z),U=o.findIndex(B=>B.value===K);return Q-U}))},f=x("checkbox",I),c=`${f}-group`,v=q(f),[d,p,V]=A(f,v),$=ce(u,["value","disabled"]),k=i.length?o.map(l=>n.createElement(X,{prefixCls:f,key:l.value.toString(),disabled:"disabled"in l?l.disabled:u.disabled,value:l.value,checked:s.includes(l.value),onChange:l.onChange,className:`${c}-item`,style:l.style,title:l.title,id:l.id,required:l.required},l.label)):a,R={toggleOption:y,value:s,disabled:u.disabled,name:u.name,registerValue:P,cancelValue:w},D=N(c,{[`${c}-rtl`]:j==="rtl"},g,m,V,v,p);return d(n.createElement("div",Object.assign({className:D,style:S},$,{ref:r}),n.createElement(L.Provider,{value:R},k)))}),J=X;J.Group=ge;J.__ANT_CHECKBOX=!0;export{be as C,J as a,pe as g};