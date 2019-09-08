<?php

namespace Vanguard\Http\Controllers\Web;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Vanguard\Events\Role\PermissionsUpdated;
use Vanguard\Http\Controllers\Controller;
use Vanguard\Http\Requests\Permission\CreatePermissionRequest;
use Vanguard\Http\Requests\Permission\UpdatePermissionRequest;
use Vanguard\Permission;
use Vanguard\Repositories\Permission\PermissionRepository;
use Vanguard\Repositories\Role\RoleRepository;
use Illuminate\Http\Request;

/**
 * Class PermissionsController
 * @package Vanguard\Http\Controllers
 */
class PermissionsController extends Controller
{
    /**
     * @var RoleRepository
     */
    private $roles;
    /**
     * @var PermissionRepository
     */
    private $permissions;

    /**
     * PermissionsController constructor.
     * @param RoleRepository $roles
     * @param PermissionRepository $permissions
     */
    public function __construct(RoleRepository $roles, PermissionRepository $permissions)
    {
        $this->middleware('auth');
        $this->middleware('permission:permissions.manage');
        $this->roles = $roles;
        $this->permissions = $permissions;
    }

    /**
     * Displays the page with all available permissions.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $roles = $this->roles->all();
        $permissions = $this->permissions->all();

        return view('permission.index', compact('roles', 'permissions'));
    }

    /**
     * Displays the form for creating new permission.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $edit = false;

        return view('permission.add-edit', compact('edit'));
    }

    /**
     * Store permission to database.
     *
     * @param CreatePermissionRequest $request
     * @return mixed
     */
    public function store(CreatePermissionRequest $request)
    {
        $this->permissions->create($request->all());

        return redirect()->route('permission.index')
            ->withSuccess(trans('app.permission_created_successfully'));
    }

    /**
     * Displays the form for editing specific permission.
     *
     * @param Permission $permission
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Permission $permission)
    {
        $edit = true;

        return view('permission.add-edit', compact('edit', 'permission'));
    }

    /**
     * Update specified permission.
     *
     * @param Permission $permission
     * @param UpdatePermissionRequest $request
     * @return mixed
     */
    public function update(Permission $permission, UpdatePermissionRequest $request)
    {
        $this->permissions->update($permission->id, $request->all());

        return redirect()->route('permission.index')
            ->withSuccess(trans('app.permission_updated_successfully'));
    }

    /**
     * Destroy the permission if it is removable.
     *
     * @param Permission $permission
     * @return mixed
     * @throws \Exception
     */
    public function destroy(Permission $permission)
    {
        if (! $permission->removable) {
            throw new NotFoundHttpException;
        }

        $this->permissions->delete($permission->id);

        return redirect()->route('permission.index')
            ->withSuccess(trans('app.permission_deleted_successfully'));
    }

    /**
     * Update permissions for each role.
     *
     * @param Request $request
     * @return mixed
     */
    public function saveRolePermissions(Request $request)
    {
        $roles = $request->get('roles');

        $allRoles = $this->roles->lists('id');

        foreach ($allRoles as $roleId) {
            $permissions = array_get($roles, $roleId, []);
            $this->roles->updatePermissions($roleId, $permissions);
        }

        event(new PermissionsUpdated);

        return redirect()->route('permission.index')
            ->withSuccess(trans('app.permissions_saved_successfully'));
    }
}