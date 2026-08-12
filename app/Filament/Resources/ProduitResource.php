<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProduitResource\Pages;
use App\Filament\Resources\ProduitResource\RelationManagers;
use App\Models\Produit;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Actions\Action; // Import important pour l'impression

class ProduitResource extends Resource
{
    protected static ?string $model = Produit::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('nom')
                    ->required(),
                TextInput::make('code_barre')
                    ->label('Code-barres (EAN)')
                    ->unique(ignoreRecord: true)
                    ->helperText('Cliquez ici et scannez le produit pour remplir automatiquement'),
                TextInput::make('prix')
                    ->numeric()
                    ->required(),
                TextInput::make('stock')
                    ->numeric()
                    ->required(),
                FileUpload::make('image')
                    ->required()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code_barre')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('nom')
                    ->limit(90),
                TextColumn::make('prix')
                    ->money('KMF'),
                TextColumn::make('stock')
                    ->numeric(),
                ImageColumn::make('image')
                    ->circular(),
            ])
            ->filters([
                //
            ])
            ->actions([
                // Action pour imprimer l'étiquette personnalisée
                Action::make('imprimer_etiquette')
                    ->label('Étiquette')
                    ->icon('heroicon-o-printer')
                    ->color('info') // Couleur bleue pour la différencier
                    ->url(fn (Produit $record): string => route('produit.etiquette', $record))
                    ->openUrlInNewTab(), // Ouvre dans un nouvel onglet pour ne pas quitter Filament
                
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProduits::route('/'),
            'create' => Pages\CreateProduit::route('/create'),
            'edit' => Pages\EditProduit::route('/{record}/edit'),
        ];
    }
}