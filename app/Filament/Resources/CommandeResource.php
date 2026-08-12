<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CommandeResource\Pages;
use App\Models\Commande;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkAction;
use Illuminate\Database\Eloquent\Collection;

class CommandeResource extends Resource
{
    protected static ?string $model = Commande::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    
    protected static ?string $navigationLabel = 'Commandes';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informations Commande')
                    ->schema([
                        Forms\Components\TextInput::make('numero_commande')
                            ->label('N° Commande')
                            ->disabled(),
                        Forms\Components\Select::make('client_id')
                            ->relationship('client', 'nom')
                            ->label('Client')
                            ->disabled(),
                        Forms\Components\TextInput::make('total')
                            ->label('Total (KMF)')
                            ->numeric()
                            ->disabled(),
                        Forms\Components\Select::make('statut')
                            ->options([
                                'en_attente' => 'En attente',
                                'livree' => 'Livrée',
                                'annulee' => 'Annulée',
                            ])
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make('Articles')
                    ->schema([
                        Forms\Components\Repeater::make('items')
                            ->relationship()
                            ->schema([
                                Forms\Components\Select::make('produit_id')
                                    ->relationship('produit', 'nom')
                                    ->label('Produit')
                                    ->disabled(),
                                Forms\Components\TextInput::make('quantite')
                                    ->numeric()
                                    ->disabled(),
                                Forms\Components\TextInput::make('prix_unitaire')
                                    ->numeric()
                                    ->disabled(),
                                Forms\Components\TextInput::make('total')
                                    ->numeric()
                                    ->disabled(),
                            ])
                            ->columns(4)
                            ->addable(false)
                            ->deletable(false)
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('numero_commande')
                    ->label('N° Ticket')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('client.nom')
                    ->label('Client')
                    ->formatStateUsing(fn ($record) => $record->client ? "{$record->client->nom} {$record->client->prenom}" : 'Client anonyme')
                    ->searchable(),
                Tables\Columns\TextColumn::make('total')
                    ->label('Montant')
                    ->formatStateUsing(fn ($state) => number_format($state, 0, '.', ' ') . ' KMF')
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('statut')
                    ->colors([
                        'warning' => 'en_attente',
                        'success' => 'livree',
                        'danger' => 'annulee',
                    ]),
                Tables\Columns\TextColumn::make('date_commande')
                    ->label('Date')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('statut')
                    ->options([
                        'en_attente' => 'En attente',
                        'livree' => 'Livrée',
                        'annulee' => 'Annulée',
                    ]),
            ])
            ->actions([
                // ACTION TICKET (Format Thermique)
                Action::make('imprimer_ticket')
                    ->label('Ticket')
                    ->icon('heroicon-o-receipt-percent')
                    ->color('success')
                    ->url(fn (Commande $record): string => route('commandes.ticket', $record))
                    ->openUrlInNewTab(), // Empêche la redirection Filament

                // ACTION FACTURE (Format A4)
                Action::make('imprimer_facture')
                    ->label('Facture')
                    ->icon('heroicon-o-document-text')
                    ->color('info')
                    ->url(fn (Commande $record): string => route('commandes.facture', $record))
                    ->openUrlInNewTab(), // Empêche la redirection Filament

                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Impression des étiquettes en masse
                    BulkAction::make('imprimer_etiquettes')
                        ->label('Imprimer Étiquettes Produits')
                        ->icon('heroicon-o-printer')
                        ->color('info')
                        ->action(function (Collection $records) {
                            $productIds = [];
                            foreach ($records as $commande) {
                                foreach ($commande->items as $item) {
                                    $productIds[] = $item->produit_id;
                                }
                            }
                            // On stocke les IDs en session pour la route
                            session(['print_ids' => array_unique($productIds)]);
                        })
                        ->url(fn () => route('produit.etiquette.masse'))
                        ->openUrlInNewTab(),

                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCommandes::route('/'),
            'create' => Pages\CreateCommande::route('/create'),
            'edit' => Pages\EditCommande::route('/{record}/edit'),
        ];
    }
}