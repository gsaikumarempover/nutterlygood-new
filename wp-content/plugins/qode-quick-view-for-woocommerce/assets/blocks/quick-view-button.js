(function() {
	"use strict";
	var __webpack_modules__ = {
		613: function(module, __webpack_exports__, __webpack_require__) {
			var _node_modules_css_loader_dist_runtime_noSourceMaps_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(601);
			var _node_modules_css_loader_dist_runtime_noSourceMaps_js__WEBPACK_IMPORTED_MODULE_0___default = __webpack_require__.n(_node_modules_css_loader_dist_runtime_noSourceMaps_js__WEBPACK_IMPORTED_MODULE_0__);
			var _node_modules_css_loader_dist_runtime_api_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(314);
			var _node_modules_css_loader_dist_runtime_api_js__WEBPACK_IMPORTED_MODULE_1___default = __webpack_require__.n(_node_modules_css_loader_dist_runtime_api_js__WEBPACK_IMPORTED_MODULE_1__);
			var ___CSS_LOADER_EXPORT___ = _node_modules_css_loader_dist_runtime_api_js__WEBPACK_IMPORTED_MODULE_1___default()(_node_modules_css_loader_dist_runtime_noSourceMaps_js__WEBPACK_IMPORTED_MODULE_0___default());
			___CSS_LOADER_EXPORT___.push([ module.id, `@keyframes qqvfw-rotate{0%{transform:rotate(0deg)}100%{transform:rotate(360deg)}}.editor-styles-wrapper .wp-block-group.woocommerce.product>.wp-block-group>.wp-block-columns>.wp-block-column,.editor-styles-wrapper .wp-block-post-template .wp-block-post.product,.editor-styles-wrapper .qqvfw-block-wrapper{position:relative}.editor-styles-wrapper .qqvfw-quick-view-button-wrapper{position:relative;z-index:10}.editor-styles-wrapper .qqvfw-quick-view-button-wrapper .qqvfw-quick-view-button.qqvfw-shortcode.button{position:relative;display:inline-flex !important;align-items:center;gap:6px}.editor-styles-wrapper .qqvfw-quick-view-button-wrapper .qqvfw-quick-view-button .qqvfw-m-icon{width:22px;flex-shrink:0}.editor-styles-wrapper .qqvfw-quick-view-button-wrapper .qqvfw-quick-view-button .qqvfw-m-icon>*{display:block;width:100%;height:auto}.editor-styles-wrapper .qqvfw-quick-view-button-wrapper .qqvfw-quick-view-button .qqvfw-m-icon svg{fill:none;stroke:currentColor}.editor-styles-wrapper .qqvfw-quick-view-button .qqvfw-m-spinner{position:absolute;top:50%;left:50%;width:100%;max-width:20px;height:auto;display:block;visibility:hidden;z-index:-1;transform:translate(-50%, -50%)}.editor-styles-wrapper .qqvfw-quick-view-button .qqvfw-m-spinner svg{display:block;width:100%;height:100%;fill:currentColor;stroke:none;animation:qqvfw-rotate 2s infinite linear}.editor-styles-wrapper .qqvfw-quick-view-button.qqvfw--loading .qqvfw-m-spinner{visibility:visible;z-index:9}.editor-styles-wrapper .qqvfw-quick-view-button.qqvfw--loading .qqvfw-m-text,.editor-styles-wrapper .qqvfw-quick-view-button.qqvfw--loading .qqvfw-m-icon{visibility:hidden;z-index:-1}.editor-styles-wrapper .qqvfw-quick-view-button[href*="#qqvfw-"].qqvfw--loading{position:relative;visibility:hidden;z-index:-1}.qwfw-wishlist-table .editor-styles-wrapper .qqvfw-quick-view-button[href*="#qqvfw-"].qqvfw--loading{z-index:1}\n`, "" ]);
			__webpack_exports__.A = ___CSS_LOADER_EXPORT___;
		},
		314: function(module) {
			module.exports = function(cssWithMappingToString) {
				var list = [];
				list.toString = function toString() {
					return this.map((function(item) {
						var content = "";
						var needLayer = typeof item[5] !== "undefined";
						if (item[4]) {
							content += "@supports (".concat(item[4], ") {");
						}
						if (item[2]) {
							content += "@media ".concat(item[2], " {");
						}
						if (needLayer) {
							content += "@layer".concat(item[5].length > 0 ? " ".concat(item[5]) : "", " {");
						}
						content += cssWithMappingToString(item);
						if (needLayer) {
							content += "}";
						}
						if (item[2]) {
							content += "}";
						}
						if (item[4]) {
							content += "}";
						}
						return content;
					})).join("");
				};
				list.i = function i(modules, media, dedupe, supports, layer) {
					if (typeof modules === "string") {
						modules = [ [ null, modules, undefined ] ];
					}
					var alreadyImportedModules = {};
					if (dedupe) {
						for (var k = 0; k < this.length; k++) {
							var id = this[k][0];
							if (id != null) {
								alreadyImportedModules[id] = true;
							}
						}
					}
					for (var _k = 0; _k < modules.length; _k++) {
						var item = [].concat(modules[_k]);
						if (dedupe && alreadyImportedModules[item[0]]) {
							continue;
						}
						if (typeof layer !== "undefined") {
							if (typeof item[5] === "undefined") {
								item[5] = layer;
							} else {
								item[1] = "@layer".concat(item[5].length > 0 ? " ".concat(item[5]) : "", " {").concat(item[1], "}");
								item[5] = layer;
							}
						}
						if (media) {
							if (!item[2]) {
								item[2] = media;
							} else {
								item[1] = "@media ".concat(item[2], " {").concat(item[1], "}");
								item[2] = media;
							}
						}
						if (supports) {
							if (!item[4]) {
								item[4] = "".concat(supports);
							} else {
								item[1] = "@supports (".concat(item[4], ") {").concat(item[1], "}");
								item[4] = supports;
							}
						}
						list.push(item);
					}
				};
				return list;
			};
		},
		601: function(module) {
			module.exports = function(i) {
				return i[1];
			};
		},
		72: function(module) {
			var stylesInDOM = [];
			function getIndexByIdentifier(identifier) {
				var result = -1;
				for (var i = 0; i < stylesInDOM.length; i++) {
					if (stylesInDOM[i].identifier === identifier) {
						result = i;
						break;
					}
				}
				return result;
			}
			function modulesToDom(list, options) {
				var idCountMap = {};
				var identifiers = [];
				for (var i = 0; i < list.length; i++) {
					var item = list[i];
					var id = options.base ? item[0] + options.base : item[0];
					var count = idCountMap[id] || 0;
					var identifier = "".concat(id, " ").concat(count);
					idCountMap[id] = count + 1;
					var indexByIdentifier = getIndexByIdentifier(identifier);
					var obj = {
						css: item[1],
						media: item[2],
						sourceMap: item[3],
						supports: item[4],
						layer: item[5]
					};
					if (indexByIdentifier !== -1) {
						stylesInDOM[indexByIdentifier].references++;
						stylesInDOM[indexByIdentifier].updater(obj);
					} else {
						var updater = addElementStyle(obj, options);
						options.byIndex = i;
						stylesInDOM.splice(i, 0, {
							identifier,
							updater,
							references: 1
						});
					}
					identifiers.push(identifier);
				}
				return identifiers;
			}
			function addElementStyle(obj, options) {
				var api = options.domAPI(options);
				api.update(obj);
				var updater = function updater(newObj) {
					if (newObj) {
						if (newObj.css === obj.css && newObj.media === obj.media && newObj.sourceMap === obj.sourceMap && newObj.supports === obj.supports && newObj.layer === obj.layer) {
							return;
						}
						api.update(obj = newObj);
					} else {
						api.remove();
					}
				};
				return updater;
			}
			module.exports = function(list, options) {
				options = options || {};
				list = list || [];
				var lastIdentifiers = modulesToDom(list, options);
				return function update(newList) {
					newList = newList || [];
					for (var i = 0; i < lastIdentifiers.length; i++) {
						var identifier = lastIdentifiers[i];
						var index = getIndexByIdentifier(identifier);
						stylesInDOM[index].references--;
					}
					var newLastIdentifiers = modulesToDom(newList, options);
					for (var _i = 0; _i < lastIdentifiers.length; _i++) {
						var _identifier = lastIdentifiers[_i];
						var _index = getIndexByIdentifier(_identifier);
						if (stylesInDOM[_index].references === 0) {
							stylesInDOM[_index].updater();
							stylesInDOM.splice(_index, 1);
						}
					}
					lastIdentifiers = newLastIdentifiers;
				};
			};
		},
		659: function(module) {
			var memo = {};
			function getTarget(target) {
				if (typeof memo[target] === "undefined") {
					var styleTarget = document.querySelector(target);
					if (window.HTMLIFrameElement && styleTarget instanceof window.HTMLIFrameElement) {
						try {
							styleTarget = styleTarget.contentDocument.head;
						} catch (e) {
							styleTarget = null;
						}
					}
					memo[target] = styleTarget;
				}
				return memo[target];
			}
			function insertBySelector(insert, style) {
				var target = getTarget(insert);
				if (!target) {
					throw new Error("Couldn't find a style target. This probably means that the value for the 'insert' parameter is invalid.");
				}
				target.appendChild(style);
			}
			module.exports = insertBySelector;
		},
		540: function(module) {
			function insertStyleElement(options) {
				var element = document.createElement("style");
				options.setAttributes(element, options.attributes);
				options.insert(element, options.options);
				return element;
			}
			module.exports = insertStyleElement;
		},
		56: function(module, __unused_webpack_exports, __webpack_require__) {
			function setAttributesWithoutAttributes(styleElement) {
				var nonce = true ? __webpack_require__.nc : 0;
				if (nonce) {
					styleElement.setAttribute("nonce", nonce);
				}
			}
			module.exports = setAttributesWithoutAttributes;
		},
		825: function(module) {
			function apply(styleElement, options, obj) {
				var css = "";
				if (obj.supports) {
					css += "@supports (".concat(obj.supports, ") {");
				}
				if (obj.media) {
					css += "@media ".concat(obj.media, " {");
				}
				var needLayer = typeof obj.layer !== "undefined";
				if (needLayer) {
					css += "@layer".concat(obj.layer.length > 0 ? " ".concat(obj.layer) : "", " {");
				}
				css += obj.css;
				if (needLayer) {
					css += "}";
				}
				if (obj.media) {
					css += "}";
				}
				if (obj.supports) {
					css += "}";
				}
				var sourceMap = obj.sourceMap;
				if (sourceMap && typeof btoa !== "undefined") {
					css += "\n/*# sourceMappingURL=data:application/json;base64,".concat(btoa(unescape(encodeURIComponent(JSON.stringify(sourceMap)))), " */");
				}
				options.styleTagTransform(css, styleElement, options.options);
			}
			function removeStyleElement(styleElement) {
				if (styleElement.parentNode === null) {
					return false;
				}
				styleElement.parentNode.removeChild(styleElement);
			}
			function domAPI(options) {
				if (typeof document === "undefined") {
					return {
						update: function update() {},
						remove: function remove() {}
					};
				}
				var styleElement = options.insertStyleElement(options);
				return {
					update: function update(obj) {
						apply(styleElement, options, obj);
					},
					remove: function remove() {
						removeStyleElement(styleElement);
					}
				};
			}
			module.exports = domAPI;
		},
		113: function(module) {
			function styleTagTransform(css, styleElement) {
				if (styleElement.styleSheet) {
					styleElement.styleSheet.cssText = css;
				} else {
					while (styleElement.firstChild) {
						styleElement.removeChild(styleElement.firstChild);
					}
					styleElement.appendChild(document.createTextNode(css));
				}
			}
			module.exports = styleTagTransform;
		}
	};
	var __webpack_module_cache__ = {};
	function __webpack_require__(moduleId) {
		var cachedModule = __webpack_module_cache__[moduleId];
		if (cachedModule !== undefined) {
			return cachedModule.exports;
		}
		var module = __webpack_module_cache__[moduleId] = {
			id: moduleId,
			exports: {}
		};
		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
		return module.exports;
	}
	!function() {
		__webpack_require__.n = function(module) {
			var getter = module && module.__esModule ? function() {
				return module["default"];
			} : function() {
				return module;
			};
			__webpack_require__.d(getter, {
				a: getter
			});
			return getter;
		};
	}();
	!function() {
		__webpack_require__.d = function(exports, definition) {
			for (var key in definition) {
				if (__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
					Object.defineProperty(exports, key, {
						enumerable: true,
						get: definition[key]
					});
				}
			}
		};
	}();
	!function() {
		__webpack_require__.o = function(obj, prop) {
			return Object.prototype.hasOwnProperty.call(obj, prop);
		};
	}();
	!function() {
		__webpack_require__.nc = undefined;
	}();
	var __webpack_exports__ = {};
	!function() {
		var external_wp_i18n_namespaceObject = wp.i18n;
		var external_wp_blocks_namespaceObject = wp.blocks;
		var external_wp_element_namespaceObject = wp.element;
		var external_wp_apiFetch_namespaceObject = wp.apiFetch;
		var external_wp_apiFetch_default = __webpack_require__.n(external_wp_apiFetch_namespaceObject);
		var external_wp_blockEditor_namespaceObject = wp.blockEditor;
		var external_wp_components_namespaceObject = wp.components;
		class QuickViewButtonBlockEdit extends external_wp_element_namespaceObject.Component {
			constructor() {
				super(...arguments);
				this.state = {
					elementLoading: false,
					blockHTMLContent: ""
				};
			}
			componentDidMount() {
				const {attributes} = this.props;
				const {item_id, button_type} = attributes;
				this.setState({
					elementLoading: true
				});
				external_wp_apiFetch_default()({
					method: "POST",
					path: "/qode-quick-view-for-woocommerce/v1/render-quick-view-button",
					data: {
						item_id,
						button_type
					}
				}).then((response => {
					this.setState({
						elementLoading: false
					});
					if ("success" === response.status) {
						this.setState({
							blockHTMLContent: response.data
						});
					}
				}));
			}
			componentDidUpdate(prevProps) {
				const {attributes} = this.props;
				const {item_id, button_type} = attributes;
				if (prevProps.attributes.item_id !== item_id || prevProps.attributes.button_type !== button_type) {
					this.setState({
						elementLoading: true
					});
					external_wp_apiFetch_default()({
						method: "POST",
						path: "/qode-quick-view-for-woocommerce/v1/render-quick-view-button",
						data: {
							item_id,
							button_type
						}
					}).then((response => {
						this.setState({
							elementLoading: false
						});
						if ("success" === response.status) {
							this.setState({
								blockHTMLContent: response.data
							});
						}
					}));
				}
			}
			render() {
				const stateInstance = {
					...this.state
				};
				const {attributes, setAttributes} = this.props;
				const {item_id, button_type} = attributes;
				let blockHTMLContent = stateInstance.blockHTMLContent;
				const productList = window.qodeQuickViewForWooCommerceAdminGlobal.product_list ?? [];
				let productListOptions = [ {
					value: "",
					label: (0, external_wp_i18n_namespaceObject.__)("--Choose Product--", "qode-quick-view-for-woocommerce")
				} ];
				if (productList) {
					for (const key in productList) {
						productListOptions.push({
							value: key,
							label: productList[key]
						});
					}
				}
				return wp.element.createElement(wp.element.Fragment, null, wp.element.createElement(external_wp_blockEditor_namespaceObject.InspectorControls, null, wp.element.createElement(external_wp_components_namespaceObject.BaseControl, {
					className: "qode-woocommerce-base-control-container"
				}, wp.element.createElement(external_wp_components_namespaceObject.SelectControl, {
					label: (0, external_wp_i18n_namespaceObject.__)("Choose Product", "qode-quick-view-for-woocommerce"),
					value: item_id,
					options: productListOptions,
					onChange: value => setAttributes({
						item_id: parseInt(value, 10)
					})
				}), wp.element.createElement(external_wp_components_namespaceObject.SelectControl, {
					label: (0, external_wp_i18n_namespaceObject.__)("Button Type", "qode-quick-view-for-woocommerce"),
					value: button_type,
					options: [ {
						value: "",
						label: (0, external_wp_i18n_namespaceObject.__)("Default", "qode-quick-view-for-woocommerce")
					}, {
						value: "icon-with-text",
						label: (0, external_wp_i18n_namespaceObject.__)("Icon with Text", "qode-quick-view-for-woocommerce")
					}, {
						value: "icon",
						label: (0, external_wp_i18n_namespaceObject.__)("Only Icon", "qode-quick-view-for-woocommerce")
					}, {
						value: "text",
						label: (0, external_wp_i18n_namespaceObject.__)("Only Text", "qi-blocks")
					} ],
					onChange: button_type => setAttributes({
						button_type
					})
				}))), stateInstance.elementLoading ? wp.element.createElement("div", {
					className: "qode-woocommerce-block"
				}, wp.element.createElement(external_wp_components_namespaceObject.Spinner, null)) : wp.element.createElement("div", {
					className: "qode-woocommerce-block qqvfw--quick-view-button",
					dangerouslySetInnerHTML: {
						__html: stateInstance.blockHTMLContent ? blockHTMLContent : (0, external_wp_i18n_namespaceObject.__)("Please choose an Product", "qode-quick-view-for-woocommerce")
					}
				}));
			}
		}
		var edit = QuickViewButtonBlockEdit;
		const blockName = "qode-quick-view-for-woocommerce/quick-view-button";
		(0, external_wp_blocks_namespaceObject.registerBlockType)(blockName, {
			icon: {
				src: wp.element.createElement("svg", {
					xmlns: "http://www.w3.org/2000/svg",
					width: "16",
					height: "16",
					viewBox: "0 0 16 16"
				}, wp.element.createElement("path", {
					d: "M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z"
				}), wp.element.createElement("path", {
					d: "M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8zm8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z"
				}))
			},
			title: (0, external_wp_i18n_namespaceObject.__)("Qode Quick View Button", "qode-quick-view-for-woocommerce"),
			description: (0, external_wp_i18n_namespaceObject.__)("Block that displays quick view button", "qode-quick-view-for-woocommerce"),
			category: "qode-woocommerce-blocks",
			keywords: [ (0, external_wp_i18n_namespaceObject.__)("product", "qode-quick-view-for-woocommerce"), (0,
				external_wp_i18n_namespaceObject.__)("woocommerce", "qode-quick-view-for-woocommerce"), (0,
				external_wp_i18n_namespaceObject.__)("quick-view", "qode-quick-view-for-woocommercee"), (0,
				external_wp_i18n_namespaceObject.__)("qode", "qode-quick-view-for-woocommerce") ],
			edit,
			attributes: {
				item_id: {
					type: "number",
					default: ""
				},
				button_type: {
					type: "string",
					default: ""
				}
			}
		});
	}();
	!function() {
		var _node_modules_style_loader_dist_runtime_injectStylesIntoStyleTag_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(72);
		var _node_modules_style_loader_dist_runtime_injectStylesIntoStyleTag_js__WEBPACK_IMPORTED_MODULE_0___default = __webpack_require__.n(_node_modules_style_loader_dist_runtime_injectStylesIntoStyleTag_js__WEBPACK_IMPORTED_MODULE_0__);
		var _node_modules_style_loader_dist_runtime_styleDomAPI_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(825);
		var _node_modules_style_loader_dist_runtime_styleDomAPI_js__WEBPACK_IMPORTED_MODULE_1___default = __webpack_require__.n(_node_modules_style_loader_dist_runtime_styleDomAPI_js__WEBPACK_IMPORTED_MODULE_1__);
		var _node_modules_style_loader_dist_runtime_insertBySelector_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(659);
		var _node_modules_style_loader_dist_runtime_insertBySelector_js__WEBPACK_IMPORTED_MODULE_2___default = __webpack_require__.n(_node_modules_style_loader_dist_runtime_insertBySelector_js__WEBPACK_IMPORTED_MODULE_2__);
		var _node_modules_style_loader_dist_runtime_setAttributesWithoutAttributes_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(56);
		var _node_modules_style_loader_dist_runtime_setAttributesWithoutAttributes_js__WEBPACK_IMPORTED_MODULE_3___default = __webpack_require__.n(_node_modules_style_loader_dist_runtime_setAttributesWithoutAttributes_js__WEBPACK_IMPORTED_MODULE_3__);
		var _node_modules_style_loader_dist_runtime_insertStyleElement_js__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(540);
		var _node_modules_style_loader_dist_runtime_insertStyleElement_js__WEBPACK_IMPORTED_MODULE_4___default = __webpack_require__.n(_node_modules_style_loader_dist_runtime_insertStyleElement_js__WEBPACK_IMPORTED_MODULE_4__);
		var _node_modules_style_loader_dist_runtime_styleTagTransform_js__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(113);
		var _node_modules_style_loader_dist_runtime_styleTagTransform_js__WEBPACK_IMPORTED_MODULE_5___default = __webpack_require__.n(_node_modules_style_loader_dist_runtime_styleTagTransform_js__WEBPACK_IMPORTED_MODULE_5__);
		var _node_modules_css_loader_dist_cjs_js_node_modules_postcss_loader_dist_cjs_js_ruleSet_1_rules_1_use_2_node_modules_sass_loader_dist_cjs_js_node_modules_webpack_import_glob_loader_index_js_quick_view_button_block_scss__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(613);
		var options = {};
		options.styleTagTransform = _node_modules_style_loader_dist_runtime_styleTagTransform_js__WEBPACK_IMPORTED_MODULE_5___default();
		options.setAttributes = _node_modules_style_loader_dist_runtime_setAttributesWithoutAttributes_js__WEBPACK_IMPORTED_MODULE_3___default();
		options.insert = _node_modules_style_loader_dist_runtime_insertBySelector_js__WEBPACK_IMPORTED_MODULE_2___default().bind(null, "head");
		options.domAPI = _node_modules_style_loader_dist_runtime_styleDomAPI_js__WEBPACK_IMPORTED_MODULE_1___default();
		options.insertStyleElement = _node_modules_style_loader_dist_runtime_insertStyleElement_js__WEBPACK_IMPORTED_MODULE_4___default();
		var update = _node_modules_style_loader_dist_runtime_injectStylesIntoStyleTag_js__WEBPACK_IMPORTED_MODULE_0___default()(_node_modules_css_loader_dist_cjs_js_node_modules_postcss_loader_dist_cjs_js_ruleSet_1_rules_1_use_2_node_modules_sass_loader_dist_cjs_js_node_modules_webpack_import_glob_loader_index_js_quick_view_button_block_scss__WEBPACK_IMPORTED_MODULE_6__.A, options);
		var __WEBPACK_DEFAULT_EXPORT__ = _node_modules_css_loader_dist_cjs_js_node_modules_postcss_loader_dist_cjs_js_ruleSet_1_rules_1_use_2_node_modules_sass_loader_dist_cjs_js_node_modules_webpack_import_glob_loader_index_js_quick_view_button_block_scss__WEBPACK_IMPORTED_MODULE_6__.A && _node_modules_css_loader_dist_cjs_js_node_modules_postcss_loader_dist_cjs_js_ruleSet_1_rules_1_use_2_node_modules_sass_loader_dist_cjs_js_node_modules_webpack_import_glob_loader_index_js_quick_view_button_block_scss__WEBPACK_IMPORTED_MODULE_6__.A.locals ? _node_modules_css_loader_dist_cjs_js_node_modules_postcss_loader_dist_cjs_js_ruleSet_1_rules_1_use_2_node_modules_sass_loader_dist_cjs_js_node_modules_webpack_import_glob_loader_index_js_quick_view_button_block_scss__WEBPACK_IMPORTED_MODULE_6__.A.locals : undefined;
	}();
})();