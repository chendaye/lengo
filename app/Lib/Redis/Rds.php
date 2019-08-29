<?php

namespace Lib\Redis;

use Illuminate\Support\Facades\Redis;

/**
 * 分布式图片服务器
 *
 * @author long
 */
class Rds
{
    /**
     * 一篇文章对应的分类id，缓存 key
     *
     * @param [type] $id
     * @return void
     * @author chendaye
     */
   static public function articleCategoryKey($id)
   {
        return 'Article:'.$id.':CategoryId';
   }

   /**
    * 文章分类详情
    *
    * @param [type] $id
    * @return void
    * @author chendaye
    */
   static public function articleCategoryDetail($id)
   {
        return 'Article:' . $id . ':CategoryDetail';
   }

   /**
    * 文章标签的详情
    *
    * @param [type] $id
    * @return void
    * @author chendaye
    */
   static public function articleTagDetail($id)
   {
        return 'Article:' . $id . ':TagDetail';
   }

   /**
    * 文章详情
    *
    * @param [type] $id
    * @return void
    * @author chendaye
    */
   static public function articleDetail($id)
   {
        return 'Article:' . $id . ':Detail';
   }

   /**
    * 文章归档信息
    *
    * @return void
    * @author chendaye
    */
   static public function articleArchives()
   {
       return 'Article:Archives';
   }

   /**
    * 文章数量统计信息
    *
    * @return void
    * @author chendaye
    */
   static public function articleCount()
   {
       return 'Article:Count';
   }

   /**
    * 当前分类的下一级分类
    *
    * @param [type] $id
    * @return void
    * @author chendaye
    */
   static public function categoryNext($id)
   {
       return 'Category:'.$id.'Next';
   }

   /**
    * 整个分类树
    *
    * @return void
    * @author chendaye
    */
   static public function categoryTree()
   {
        return 'Category:Tree';
   }

   /**
    * 递归获取所有子分类
    *
    * @param [type] $id
    * @return void
    * @author chendaye
    */
   static public function categoryAllNext($id)
   {
        return 'Category:' . $id . 'All:Next';
   }

   /**
    * 文章评论
    *
    * @param [type] $id
    * @return void
    * @author chendaye
    */
   static public function commentArticle($id)
   {
       return 'Article:'.$id.':Comment';
   }

   /**
    * 获取友链
    *
    * @return void
    * @author chendaye
    */
   static public function friendsLink()
   {
       return 'Friends:Link';
   }

   /**
    * 序列化数据，已字符串保存
    *
    * @param [type] $key
    * @param [type] $data
    * @param integer $ttl
    * @return void
    * @author chendaye
    */
   static public function set($key, $data, $ttl = 72000)
   {
        Redis::setEx($key, $ttl, serialize($data));
   }

   /**
    * 获取数据并且反序列化
    *
    * @param [type] $key
    * @return void
    * @author chendaye
    */
   static public function get($key)
   {
       if(!Redis::exists($key)) return [];
       return unserialize(Redis::get($key));
   }

   /**
    * 保存10天
    *
    * @param [type] $key
    * @param [type] $data
    * @param integer $ttl
    * @return void
    * @author chendaye
    */
   static public function save($key, $data, $ttl = 72000)
   {
        Redis::hMSet($key, \is_array($data) ? $data : $data->toArray());
        Redis::expire($key, $ttl); // 10天
   }
}
