# laravel-permission

## 说明

> 用户 user 角色 role 权限 permissions
>>一个 role 可以对应多个权限  role_has_permissions
>> 一个 user 可以有多个权限  model_has_permissions
>>> 一个 user 可以有多个 role model_has_roles  注意：model_id = user.id;
user 可以继承它所有的 role 的权限

