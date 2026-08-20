import { MaskInput as V } from "./maska.js";
//#region node_modules/maska/dist/vue.mjs
var l = /* @__PURE__ */ new WeakMap(), c = (e, s) => {
	if (e.arg == null || e.instance == null) return;
	const a = "setup" in e.instance.$.type;
	e.arg in e.instance ? e.instance[e.arg] = s : a && console.warn("Maska: please expose `%s` using defineExpose", e.arg);
}, k = (e, s) => {
	var u;
	const a = e instanceof HTMLInputElement ? e : e.querySelector("input");
	if (a == null || (a == null ? void 0 : a.type) === "file") return;
	let t = {};
	if (s.value != null && (t = typeof s.value == "string" ? { mask: s.value } : { ...s.value }), s.arg != null) {
		const o = (r) => {
			c(s, s.modifiers.unmasked ? r.unmasked : s.modifiers.completed ? r.completed : r.masked);
		};
		t.onMaska = t.onMaska == null ? o : Array.isArray(t.onMaska) ? [...t.onMaska, o] : [t.onMaska, o];
	}
	l.has(a) ? (u = l.get(a)) == null || u.update(t) : l.set(a, new V(a, t));
};
//#endregion
export { k as vMaska };

//# sourceMappingURL=maska_vue.js.map