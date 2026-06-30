<?php

namespace App\Models;

use Database\Factories\GradePredicateFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['min_score', 'max_score', 'predicate', 'description'])]
class GradePredicate extends Model
{
    /** @use HasFactory<GradePredicateFactory> */
    use HasFactory;
}
