<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class TaskMonitoring extends Model
{
    use HasFactory;

    protected static function booted(): void
    {
        static::saving(function (TaskMonitoring $taskMonitoring): void {
            if (strtolower(trim((string) $taskMonitoring->submission_decision)) === 'accepted') {
                $taskMonitoring->submission_status = 'completed';
            }
        });
    }

    /**
     * @var list<string>
     */
    protected $fillable = [
        'date_task_received',
        'client_id',
        'task_id',
        'assigned_responsible_person_id',
        'required_forms_documents',
        'submission_status',
        'date_of_submission',
        'receiving_officer',
        'acknowledgement_receipt_reference_number',
        'submission_decision',
        'submission_notes',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'date_task_received' => 'date',
            'required_forms_documents' => 'array',
            'date_of_submission' => 'date',
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
