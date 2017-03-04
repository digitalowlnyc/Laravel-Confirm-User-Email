<?php

namespace BlueNest\ConfirmUserEmail\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ConfirmationCode extends Model
{
    protected $table = "confirmation_codes";

    protected $primaryKey = 'confirmation_code';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'confirmation_code', 'expired',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
    ];

    public function expire() {
        $this->expired = 1;
        $this->save();
    }

    public static function existsFor($userId) {
        return ConfirmationCode::where(['user_id' => $userId, 'expired' => 0])->exists();
    }

    public static function generateConfirmationCode($userId) {
        $generatedCode = str_random(16);

        $confirmationRecord = ConfirmationCode::firstOrNew(['user_id' => $userId]);
        $confirmationRecord->expired = 0;
        $confirmationRecord->confirmation_code = $generatedCode;
        $confirmationRecord->save();

        return $generatedCode;
    }
}
