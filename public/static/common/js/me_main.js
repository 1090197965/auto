/**
 * Created by Administrator on 2017-12-13.
 */
(function(){
	var getType = function(value, defa){
		if(typeof value == 'undefined'){
			return defa;
		}else{
			return value;
		}
	};

	//弹窗
	var $alert = {
		_type : function(value, title, type, call, errorCall){
			if(typeof value == 'object'){
				type = value.type;
				value = value.message;
				if(type == 'success')
					call = call;
				else if(type == 'error')
					call = errorCall;
				else{
					call = undefined;
				}

			}else{
				type = getType(type, 'info');
			}

			var config = {
				text 					: value,
				type					: type,
				allowOutsideClick		: true,
				showLoaderOnConfirm	: true
			};

			switch(type){
				case 'success':
					title = getType(title, '操作成功');
					config['title'] = title;
					break;

				case 'error':
					title = getType(title, '操作失败');
					config['title'] = title;
					break;

				case 'timer':
					type = title;
					if(type === true){
						type = 'success';
					}else if(type === false){
						type = 'error';
					}else{
						type = getType(type, null);
					}

					config['title'] = value;
					config['text'] = '三秒后自动关闭窗口';
					config['timer'] = 3000;
					config['showConfirmButton'] = false;
					config['type'] = type;

					//因为时间类型不能有回调, 所以这里单独调用
					//swal(config);
					//return true;
					break;

				default :
					title = getType(title, '提示信息');
					config['title'] = title;
					break;
			}

			if(typeof call == 'undefined'){
				swal(config);

			}else{
				swal(config, call);

			}
		},
		info	: function(value, title, call){
			this._type(value, title, 'info', call);
		},
		auto	: function(value, sucessCall, errorCall){
			this._type(value, undefined, undefined, sucessCall, errorCall);
		},
		error	: function(value, title){
			this._type(value, title, 'error');
		},
		success	: function(value, title, call){
			this._type(value, title, 'success', call);
		},
		timer	: function(value, type){
		    this._type(value, type, 'timer');
		},
		input	: function(value, title, check, call, success, error){
			check = getType(check, function(inputValue){
				if (inputValue === "") {
					return false;
				}else if(inputValue == '123'){
					return false;
				}else{
					return true;
				}
			});

			swal({
				title: title,
				text: value,
				type: 'input',
				showCancelButton: true,
				allowOutsideClick		: true,
				showLoaderOnConfirm	: true,
				closeOnConfirm: false,
				animation: "slide-from-top",
				inputPlaceholder: title
			},
			function(inputValue){
				var _error = error;
				if(inputValue === ""){
					errorValue = '输入不能为空!';
				}else{
					errorValue = '输入错误, 请重试!';
				}
				_error = getType(_error, errorValue);
				success = getType(success, '操作成功!');

				if(check(inputValue)){
					$alert.success(success, '输入成功!', function(){
						setTimeout(function(){
							swal("Ajax请求完成！");
						}, 2000);
					});
				}else{
					swal.showInputError(_error);
				}

				return inputValue;
			});
		},
		is		: function(title, message, call, url){
			swal({
					title: title,
					text: message,
					type: "warning",
					showCancelButton: true,
					confirmButtonColor: "#DD6B55",
					confirmButtonText: "确定",
					cancelButtonText: "取消",
					showLoaderOnConfirm	: true,
					closeOnConfirm: false,
					closeOnCancel: true
				},
				function(isConfirm){
					if (isConfirm) {
						call();
					}
				});
		},
		delete	: function(message, url, call){
			message = getType(message, '删除后将无法恢复!');
			var deleteHandle = function(){
				$.get(url, function(data){
					$alert.auto(data, call);
				}, 'json');
			};
			$alert.is('确定要删除吗?', message, deleteHandle, url);
			//swal({
			//	title: "确定删除吗？",
			//	text: message,
			//	type: "warning",
			//	showCancelButton: true,
			//	confirmButtonColor: "#DD6B55",
			//	confirmButtonText: "确定",
			//	cancelButtonText: "取消",
			//	showLoaderOnConfirm	: true,
			//	closeOnConfirm: false,
			//	closeOnCancel: false
			//},
			//function(isConfirm){
			//	if (isConfirm) {
			//		$.get(url, function(data){
			//			$alert.auto(data, call);
			//		}, 'json');
			//	}else{
			//		$alert.error("操作已取消!", "取消！");
			//	}
			//});
		},
		html 	: function(html, title){
			swal({
				title: title,
				text:  html,
				html: true,
				allowOutsideClick		: true,
			});
		},
		message	: function(type, message){
			toastr.options = {
				closeButton: true,
				progressBar: true,
				showMethod: 'slideDown',
				timeOut: 4000
			};
			if(type === null){
				toastr._type('提示信息', message);
			}else if(type){
				toastr.success('操作成功', message);
			}else{
				toastr.error('操作失败', message);
			}

		},
		messageB: function(message){
			$.gritter.add({
				title: '提示信息',
				text: message,
				sticky: false,
				time: 3000,
				speed:500,
				position: 'bottom-right',
				class_name: 'gritter-success'//gritter-center
			});
		},
		iframe: function(title, url, size, $isNotParent){
			//判断是否是iframe窗口
			//如果是iframe中, 就在父类窗口打开iframe, 这样就可以避免iframe窗口越来越小
			//如果有异常弹不出layer, 则把下面的trycatch注释掉, 这样应该就能调用了

			//在顶级窗口摊开 如果有table无法刷新的bug, 就使用下面的tab栏目
			var layer = parent.layer;
			//在tab栏目中弹开
			//var	layer = layui.layer;

			size = getType(size, ['993px', '500px']);
			layer.open({
				type: 2,
				title: title,
				shadeClose: true,
				shade: 0.8,
				maxmin: true, //开启最大化最小化按钮
				area: size,
				content: url
			});
		},
		close: function(data){
			data = getType(data, {result:true});

			//是否为顶级窗口
			//if(parent.name == ''){

			//}else{
				//注意：parent 是 JS 自带的全局对象，可用于操作父页面
				var index = parent.layer.getFrameIndex(window.name); //获取窗口索引

				if(data.result){
					parent.$alert.message(true, '操作成功');
					//刷新列表页面
					parent.$('#layer-reload').change();
					parent.layer.close(index);	//关闭窗口

				}else{
					parent.layer.msg(data.message, {shade: 0.3,shadeClose :true}); //提示信息
				}
			//}
		}
	};

	window.$alert = $alert;
}());