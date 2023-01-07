<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Str;

class ModelPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function before($user, $ability)
    {
        if ($user->super_admin) {
            return true;
        }
    }

    public function __call($name, $arguments)
    {
        // remove Policy word from the class name (CategoryPolicy => Category)
        $class_name = str_replace('Policy', '', class_basename($this)); // class_basename($this) : تعيد اسم الكلاس
        $class_name = Str::plural(Str::lower($class_name)); // make the class in plural (categories)

        if ($name == 'viewAny') { // we dont have viewany policy in DB
            $name = 'view';
        }
        $ability = $class_name . '.' . Str::kebab($name); // (categories.view-any)
        $user = $arguments[0]; // because the user is the first parameter in plicies classes

        if (isset($arguments[1])) { // to check the user
            $model = $arguments[1];
            if ($model->store_id !== $user->store_id) {
                return false;
            }
        }

        return $user->hasAbility($ability);
    }
}
