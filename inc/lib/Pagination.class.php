<?php
/**
*	Класс для постраничной навигации
*	Данный файл входит в состав системы IoT Core System
*	Разработчик: Роман Сергеевич Гринько
*	E-mail: rsgrinko@gmail.com
*	Сайт: https://it-stories.ru
*/

class Pagination {
	private static $page;
	private static $total;
	private static $total_pages;
	private static $limit;

	/**
	 * Задание первоначальных данных для пагинации
	 * 
	 * @param int $page
	 * @param int $total
	 * @param int $limit
	 */
	public static function execute($page, $total, $limit):void
	{
		self::$page = $page;
		self::$total = $total;
		self::$limit = $limit;
	}
	
	/**
	 * Формирование лимита для выборки из базы элементов текущей страницы
	 * 
	 * @return string
	 */
	public static function getLimit():string
	{
		self::$total_pages = intval((self::$total - 1) / self::$limit) + 1;
		if(empty(self::$page) or self::$page < 0) {
			self::$page = 1;
		}
		if(self::$page > self::$total_pages) {
			self::$page = self::$total_pages;
		}
		$start = self::$page * self::$limit - self::$limit;
		$limit = $start.', '.self::$limit;
		
		return $limit;
		
	}

	/**
	 * Вывод пагинации на страницу
	 * 
	 * @param string $paginator_name
	 * @param array $params
	 */
	public static function show($paginator_name = 'page', $params = []):void
	{
		$url = '?';
		foreach($params as $key=>$value){
			$url .= $key.'='.$value.'&';
		}
		
		if (self::$page != 1) {
			$pervpage = '<li class="page-item"><a class="page-link" href="'.$url.$paginator_name.'='.(self::$page - 1).'" aria-label="Previous"><span aria-hidden="true">«</span><span class="sr-only">Previous</span></a></li>';
		}
		if (self::$page != self::$total_pages) {
			$nextpage = '<li class="page-item"><a class="page-link" href="'.$url.$paginator_name.'='.(self::$page + 1).'" aria-label="Next"><span aria-hidden="true">»</span><span class="sr-only">Next</span></a></li>';
		}
        if(self::$page - 4 > 0) {
            $page4left = '<li class="page-item"><a class="page-link" href="'.$url.$paginator_name.'='.(self::$page - 4).'">'.(self::$page - 4).'</a></li>';
        }
        if(self::$page - 3 > 0) {
            $page3left = '<li class="page-item"><a class="page-link" href="'.$url.$paginator_name.'='.(self::$page - 3).'">'.(self::$page - 3).'</a></li>';
        }
		if(self::$page - 2 > 0) {
			$page2left = '<li class="page-item"><a class="page-link" href="'.$url.$paginator_name.'='.(self::$page - 2).'">'.(self::$page - 2).'</a></li>';
		}
		if(self::$page - 1 > 0) {
			$page1left = '<li class="page-item"><a class="page-link" href="'.$url.$paginator_name.'='.(self::$page - 1).'">'.(self::$page - 1).'</a></li>';
		}
        if(self::$page + 4 <= self::$total_pages) {
            $page4right = '<li class="page-item"><a class="page-link" href="'.$url.$paginator_name.'='.(self::$page + 4).'">'.(self::$page + 4).'</a></li>';
        }
        if(self::$page + 3 <= self::$total_pages) {
            $page3right = '<li class="page-item"><a class="page-link" href="'.$url.$paginator_name.'='.(self::$page + 3).'">'.(self::$page + 3).'</a></li>';
        }
		if(self::$page + 2 <= self::$total_pages) {
			$page2right = '<li class="page-item"><a class="page-link" href="'.$url.$paginator_name.'='.(self::$page + 2).'">'.(self::$page + 2).'</a></li>';
		}
		if(self::$page + 1 <= self::$total_pages) {
			$page1right = '<li class="page-item"><a class="page-link" href="'.$url.$paginator_name.'='.(self::$page + 1).'">'.(self::$page + 1).'</a></li>';
		}


		echo '<ul class="pagination pg-primary">'.$pervpage.$page4left.$page3left.$page2left.$page1left.'<li class="page-item active"><span class="page-link">'.self::$page.'</span></li>'.$page1right.$page2right.$page3right.$page4right.$nextpage.'</ul>';
		return;
	}	
}