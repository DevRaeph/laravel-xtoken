<?php
/*
 * ██████╗ ███████╗██╗   ██╗███████╗████████╗ ██████╗ ██████╗ ███╗   ███╗
 * ██╔══██╗██╔════╝██║   ██║██╔════╝╚══██╔══╝██╔═══██╗██╔══██╗████╗ ████║
 * ██║  ██║█████╗  ██║   ██║███████╗   ██║   ██║   ██║██████╔╝██╔████╔██║
 * ██║  ██║██╔══╝  ╚██╗ ██╔╝╚════██║   ██║   ██║   ██║██╔══██╗██║╚██╔╝██║
 * ██████╔╝███████╗ ╚████╔╝ ███████║   ██║   ╚██████╔╝██║  ██║██║ ╚═╝ ██║
 * ╚═════╝ ╚══════╝  ╚═══╝  ╚══════╝   ╚═╝    ╚═════╝ ╚═╝  ╚═╝╚═╝     ╚═╝
 * ______________________________________________________________________
 * | Author:    DevStorm Solutions - rplan
 * | Project:   ds-laravel-jwttoken-project
 * | File:      DXToken.php
 * | Created:   05.02.2021
 * | Todo:
 * |_____________________________________________________________________
 */

namespace DevRaeph\XToken\Http\Model;

use Illuminate\Database\Eloquent\Model;
/**
 * DevStorm\JWTToken\Http\Model\DXToken
 *
 * @property int $id
 * @property string $identified_by
 * @property string $issued_by
 * @property string $expires_at
 * @property string|null $agent
 * @property string|null $device
 * @property int $is_banned
 * @property int $user_id
 * @property string $user_type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Model|\Eloquent $user
 * @method static \Illuminate\Database\Eloquent\Builder|DXToken newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DXToken newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DXToken query()
 * @method static \Illuminate\Database\Eloquent\Builder|DXToken whereAgent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DXToken whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DXToken whereDevice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DXToken whereExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DXToken whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DXToken whereIdentifiedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DXToken whereIsBanned($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DXToken whereIssuedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DXToken whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DXToken whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DXToken whereUserType($value)
 * @mixin \Eloquent
 */
class DXToken extends Model
{
    // Disable Laravel's mass assignment protection
    protected $guarded = [];
    protected $table = "dxtokens";

    public function user()
    {
        return $this->morphTo();
    }
}
