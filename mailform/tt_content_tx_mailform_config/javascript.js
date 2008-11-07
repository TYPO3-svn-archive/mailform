/*<![CDATA[*/
			// ***************
			// Used to connect the db/file browser with this document and the formfields on it!
			// ***************

			var browserWin="";

			function setFormValueOpenBrowser(mode,params,browserroot) {	//
				var url = browserroot+"browser.php?mode="+mode+"&bparams="+params;

				browserWin = window.open(url,"Typo3WinBrowser","height=350,width="+(mode=="db"?650:600)+",status=0,menubar=0,resizable=1,scrollbars=1");
				browserWin.focus();
			}
			
			function setFormValueFromBrowseWin(fName,value,label,exclusiveValues)	{	//
				var formObj = setFormValue_getFObj(fName)
				if (formObj && value!="--div--")	{
					fObj = formObj[fName+"_list"];
					var len = fObj.length;
						// Clear elements if exclusive values are found
					if (exclusiveValues)	{
						var m = new RegExp("(^|,)"+value+"($|,)");
						if (exclusiveValues.match(m))	{
								// the new value is exclusive
							for (a=len-1;a>=0;a--)	fObj[a] = null;
							len = 0;
						} else if (len == 1)	{
							m = new RegExp("(^|,)"+fObj.options[0].value+"($|,)");
							if (exclusiveValues.match(m))	{
									// the old value is exclusive
								fObj[0] = null;
								len = 0;
							}
						}
					}
						// Inserting element
					var setOK = 1;
					if (!formObj[fName+"_mul"] || formObj[fName+"_mul"].value==0)	{
						for (a=0;a<len;a++)	{
							if (fObj.options[a].value==value)	{
								setOK = 0;
							}
						}
					}
					if (setOK)	{
						fObj.length++;
						fObj.options[len].value = value;
						fObj.options[len].text = unescape(label);

							// Traversing list and set the hidden-field
						setHiddenFromList(fObj,formObj[fName]);
						TBE_EDITOR.fieldChanged_fName(fName,formObj[fName+"_list"]);
					}
				}
			}
			function setHiddenFromList(fObjSel,fObjHid)	{	//
				l=fObjSel.length;
				fObjHid.value="";
				for (a=0;a<l;a++)	{
					fObjHid.value+=fObjSel.options[a].value+",";
				}
			}
			function setFormValueManipulate(fName,type)	{	//
				var formObj = setFormValue_getFObj(fName)
				if (formObj)	{
					var localArray_V = new Array();
					var localArray_L = new Array();
					var localArray_S = new Array();
					var fObjSel = formObj[fName+"_list"];
					var l=fObjSel.length;
					var c=0;
					if (type=="Remove" || type=="Top" || type=="Bottom")	{
						if (type=="Top")	{
							for (a=0;a<l;a++)	{
								if (fObjSel.options[a].selected==1)	{
									localArray_V[c]=fObjSel.options[a].value;
									localArray_L[c]=fObjSel.options[a].text;
									localArray_S[c]=1;
									c++;
								}
							}
						}
						for (a=0;a<l;a++)	{
							if (fObjSel.options[a].selected!=1)	{
								localArray_V[c]=fObjSel.options[a].value;
								localArray_L[c]=fObjSel.options[a].text;
								localArray_S[c]=0;
								c++;
							}
						}
						if (type=="Bottom")	{
							for (a=0;a<l;a++)	{
								if (fObjSel.options[a].selected==1)	{
									localArray_V[c]=fObjSel.options[a].value;
									localArray_L[c]=fObjSel.options[a].text;
									localArray_S[c]=1;
									c++;
								}
							}
						}
					}
					if (type=="Down")	{
						var tC = 0;
						var tA = new Array();

						for (a=0;a<l;a++)	{
							if (fObjSel.options[a].selected!=1)	{
									// Add non-selected element:
								localArray_V[c]=fObjSel.options[a].value;
								localArray_L[c]=fObjSel.options[a].text;
								localArray_S[c]=0;
								c++;

									// Transfer any accumulated and reset:
								if (tA.length > 0)	{
									for (aa=0;aa<tA.length;aa++)	{
										localArray_V[c]=fObjSel.options[tA[aa]].value;
										localArray_L[c]=fObjSel.options[tA[aa]].text;
										localArray_S[c]=1;
										c++;
									}

									var tC = 0;
									var tA = new Array();
								}
							} else {
								tA[tC] = a;
								tC++;
							}
						}
							// Transfer any remaining:
						if (tA.length > 0)	{
							for (aa=0;aa<tA.length;aa++)	{
								localArray_V[c]=fObjSel.options[tA[aa]].value;
								localArray_L[c]=fObjSel.options[tA[aa]].text;
								localArray_S[c]=1;
								c++;
							}
						}
					}
					if (type=="Up")	{
						var tC = 0;
						var tA = new Array();
						var c = l-1;

						for (a=l-1;a>=0;a--)	{
							if (fObjSel.options[a].selected!=1)	{

									// Add non-selected element:
								localArray_V[c]=fObjSel.options[a].value;
								localArray_L[c]=fObjSel.options[a].text;
								localArray_S[c]=0;
								c--;

									// Transfer any accumulated and reset:
								if (tA.length > 0)	{
									for (aa=0;aa<tA.length;aa++)	{
										localArray_V[c]=fObjSel.options[tA[aa]].value;
										localArray_L[c]=fObjSel.options[tA[aa]].text;
										localArray_S[c]=1;
										c--;
									}

									var tC = 0;
									var tA = new Array();
								}
							} else {
								tA[tC] = a;
								tC++;
							}
						}
							// Transfer any remaining:
						if (tA.length > 0)	{
							for (aa=0;aa<tA.length;aa++)	{
								localArray_V[c]=fObjSel.options[tA[aa]].value;
								localArray_L[c]=fObjSel.options[tA[aa]].text;
								localArray_S[c]=1;
								c--;
							}
						}
						c=l;	// Restore length value in "c"
					}

						// Transfer items in temporary storage to list object:
					fObjSel.length = c;
					for (a=0;a<c;a++)	{
						fObjSel.options[a].value = localArray_V[a];
						fObjSel.options[a].text = localArray_L[a];
						fObjSel.options[a].selected = localArray_S[a];
					}
					setHiddenFromList(fObjSel,formObj[fName]);

					TBE_EDITOR.fieldChanged_fName(fName,formObj[fName+"_list"]);
				}
			}
			function setFormValue_getFObj(fName)	{	//
				var formObj = document.editform;
				if (formObj)	{
					if (formObj[fName] && formObj[fName+"_list"] && formObj[fName+"_list"].type=="select-multiple")	{
						return formObj;
					} else {
						alert("Formfields missing:\n fName: "+formObj[fName]+"\n fName_list:"+formObj[fName+"_list"]+"\n type:"+formObj[fName+"_list"].type+"\n fName:"+fName);
					}
				}
				return "";
			}

			// END: dbFileCon parts.

				/*]]>*/
