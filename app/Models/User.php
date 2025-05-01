<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use BezhanSalleh\FilamentShield\Traits\HasPanelShield;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, HasPanelShield;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function proyects()
    {
        return $this->belongsToMany(Proyect::class);
    }
    public function activities()
    {
        return $this->belongsToMany(Activity::class);
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }

    // Relaciones con usuarios en creacion y edicion

    // Accounts
    public function createdAccounts()
    {
        return $this->hasMany(Account::class, 'user_id');
    }
    public function updatedAccounts()
    {
        return $this->hasMany(Account::class, 'user_update_id');
    }

    // Fin Accounts

    // Companies
    public function createdCompanies()
    {
        return $this->hasMany(Companie::class, 'user_id');
    }

    public function updatedCompanies()
    {
        return $this->hasMany(Companie::class, 'user_update_id');
    }

    // Fin companies


    // Data
    public function createdDatas()
    {
        return $this->hasMany(Companie::class, 'user_id');
    }
    public function updatedDatas()
    {
        return $this->hasMany(Companie::class, 'user_update_id');
    }
    // Fin Data

    // Productos
    public function createdProducts()
    {
        return $this->hasMany(Product::class, 'user_id');
    }
    public function updatedProducts()
    {
        return $this->hasMany(Product::class, 'user_update_id');
    }
    // Fin Productos


    // Clients
    public function createdClients()
    {
        return $this->hasMany(Client::class, 'user_id');
    }
    public function updatedClients()
    {
        return $this->hasMany(Client::class, 'user_update_id');
    }
    // Fin Clients

    // Clients
    public function createdQuotations()
    {
        return $this->hasMany(Quotation::class, 'user_id');
    }
    public function updatedQuotations()
    {
        return $this->hasMany(Quotation::class, 'user_update_id');
    }
    // Fin Clients

    // Servicios
    public function createdServices()
    {
        return $this->hasMany(Service::class, 'user_id');
    }
    public function updatedServices()
    {
        return $this->hasMany(Service::class, 'user_update_id');
    }
    // Fin servicios

    // Contracts

    public function createdContracts()
    {
        return $this->hasMany(Contract::class, 'user_id');
    }

    public function updatedContracts()
    {
        return $this->hasMany(Contract::class, 'user_update_id');
    }
    // Fin contracts

    // Proyects
    public function createdProyects()
    {
        return $this->hasMany(Proyect::class, 'user_id');
    }
    public function updatedProyects()
    {
        return $this->hasMany(Proyect::class, 'user_update_id');
    }
    // Fin Proyects

    // Posts
    public function createdPosts()
    {
        return $this->hasMany(Post::class, 'user_id');
    }
    public function updatedPosts()
    {
        return $this->hasMany(Post::class, 'user_update_id');
    }
    // Fin Posts

    // Categories
    public function createdCategories()
    {
        return $this->hasMany(Category::class, 'user_id');
    }
    public function updatedCategories()
    {
        return $this->hasMany(Category::class, 'user_update_id');
    }
    // Fin Categories

    // Fin Relaciones con usuarios en creacion y edicion








}
