<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class TaskMonitoring extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'date_task_received',
        'client_id',
        'task_id',
        'assigned_responsible_person_id',
        'required_forms_documents',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'date_task_received' => 'date',
            'required_forms_documents' => 'array',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function assignedResponsiblePerson(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'assigned_responsible_person_id');
    }
}
