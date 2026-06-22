<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'status' => 'boolean',
        ];
    }

    public function client()
    {
        return $this->hasOne(Client::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'created_by');
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function workLogs()
    {
        return $this->hasMany(WorkLog::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function stockTransactions()
    {
        return $this->hasMany(StockTransaction::class);
    }

    public function financeTransactions()
    {
        return $this->hasMany(FinanceTransaction::class, 'created_by');
    }

    public function orderDrafts()
    {
        return $this->hasMany(OrderDraft::class);
    }

    public function pdfDownloads()
    {
        return $this->hasMany(PdfDownload::class);
    }

    public static function getUniqueClients($perPage = 20)
    {
        return static::where('role', 'customer')
            ->select('id', 'name', 'email', 'phone')
            ->distinct()
            ->paginate($perPage);
    }
}
