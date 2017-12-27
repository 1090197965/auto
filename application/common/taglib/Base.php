<?php
	/**
	 * Created by PhpStorm.
	 * User: L
	 * Date: 2017/4/20 0020
	 * Time: 9:42
	 */
	namespace app\common\taglib;
	use think\template\TagLib;
	class Base extends TagLib{
		/**
		 * 定义标签列表
		 */
		protected $tags   =  [
			// 标签定义： attr 属性列表 close 是否闭合（0 或者1 默认1） alias 标签别名 level 嵌套层次
//			'pc'     	=> ['attr' => '', 'close' => 1], //闭合标签，默认为不闭合
//			'mobile'    => ['attr' => '', 'close' => 1],
//			'echo'    	=> ['attr' => 'value', 'close' => 0],
			'isuse'		=> ['attr' => 'value', 'close' => 1],
			'unuse'		=> ['attr' => 'value', 'close' => 1],
		];

		public function tagIsuse($tag, $content)
		{
			$name     = $tag['value'];
			$name     = $this->autoBuildVar($name);
			$parse = "<?php if(isset($name) and !empty($name)): ?>" . $content . '<?php endif; ?>';

			return $parse;
		}

		public function tagUnuse($tag, $content)
		{
			$name     = $tag['value'];
			$name     = $this->autoBuildVar($name);
			$parse = "<?php if(!isset($name) and empty($name)): ?>" . $content . '<?php endif; ?>';

			return $parse;
		}

		/**
		 * 这是一个闭合标签的简单演示
		 */
		public function tagPc($tag, $content)
		{
			$parse = '<?php if(IS_PC): ?>' . $content . '<?php endif; ?>';

			return $parse;
		}

		/**
		 * 这是一个非闭合标签的简单演示
		 */
		public function tagMobile($tag, $content)
		{
			$parse = '<?php if(!IS_PC): ?>' . $content . '<?php endif; ?>';

			return $parse;
		}

		public function tagEcho($tag){
		    list($pc, $mobile) = explode(',', $tag['value']);

			if(IS_PC)
				return $pc;
			else
				return $mobile;
		}

	}