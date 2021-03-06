<?php
/**
 * Created by PhpStorm.
 * User: schubert
 * Date: 25.10.2016
 * Time: 13:15
 */

namespace SPHERE\Application\Reporting\Controlling\DirectSearch;


use SPHERE\Common\Frontend\Form\Repository\Button\Primary;
use SPHERE\Common\Frontend\Form\Repository\Button\Reset;
use SPHERE\Common\Frontend\Form\Repository\Field\AutoCompleter;
use SPHERE\Common\Frontend\Form\Repository\Field\SelectBox;
use SPHERE\Common\Frontend\Form\Repository\Field\TextField;
use SPHERE\Common\Frontend\Form\Structure\Form;
use SPHERE\Common\Frontend\Form\Structure\FormColumn;
use SPHERE\Common\Frontend\Form\Structure\FormGroup;
use SPHERE\Common\Frontend\Form\Structure\FormRow;
use SPHERE\Common\Frontend\Icon\Repository\Search;
use SPHERE\Common\Frontend\Layout\Repository\Panel;
use SPHERE\Common\Frontend\Layout\Repository\Title;
use SPHERE\Common\Frontend\Table\Repository\Title as TableTitle;
use SPHERE\Common\Frontend\Layout\Structure\Layout;
use SPHERE\Common\Frontend\Layout\Structure\LayoutColumn;
use SPHERE\Common\Frontend\Layout\Structure\LayoutGroup;
use SPHERE\Common\Frontend\Layout\Structure\LayoutRow;
use SPHERE\Common\Frontend\Link\Repository\Standard;
use SPHERE\Common\Frontend\Message\Repository\Warning;
use SPHERE\Common\Frontend\Table\Structure\TableData;
use SPHERE\Common\Frontend\Text\Repository\Bold;
use SPHERE\Common\Window\Navigation\Link\Route;
use SPHERE\Common\Window\Stage;
use SPHERE\System\Extension\Extension;

class Frontend extends Extension
{

	private function buttonStageDirectSearch(Stage $Stage)
	{
		$Stage->addButton(
			new Standard('Teilenummer', new Route(__NAMESPACE__ . '/PartNumber'))
		);
		$Stage->addButton(
			new Standard('Produktmanager', new Route(__NAMESPACE__ . '/ProductManager'))
		);
		$Stage->addButton(
			new Standard('Marketingcode', new Route(__NAMESPACE__ . '/MarketingCode'))
		);
	}

	/**
	 * @param null|array $Search
	 * @return Stage
	 */
	public function frontendSearchPartNumber( $Search = null )
	{
		$Stage = new Stage('Direktsuche', 'Teilenummer');
		$this->buttonStageDirectSearch($Stage);

		//$this->getDebugger()->screenDump( $Search );

		$LayoutGroupDirectSearch = '';
		$LayoutGroupCompetition = '';
		if( $Search ) {
			if (empty($Result)) {

				$LayoutGroupDirectSearch =
					new LayoutGroup(
						array(
							new LayoutRow(
								array(
									new LayoutColumn(
										$this->tableMasterDataPartNumber()
										, 6
									),
									new LayoutColumn( '&nbsp;', 1 ),
									new LayoutColumn( $this->tablePriceDataPartNumber(), 5)
								)
							),
							new LayoutRow(
								new LayoutColumn(
									$this->tablePriceDevelopmentPartNumber(), 12
								)
							),
							new LayoutRow(
								new LayoutColumn(
									$this->tableSalesDataPartNumber()
									, 12
								)
							),
							new LayoutRow(
								new LayoutColumn(
									'&nbsp;'
								)
							),
							new LayoutRow(
								new LayoutColumn(
									'Diagramme', 12
								)
							)
						),
						new Title('Direktsuche')
					);

				//Zusatzinformationen, wenn Q440-Teilenummer
				if( substr( $Search['PartNumber'], 0, 4) == 'Q440' ) {
					$ExtraPartNumber =
						new LayoutRow(
							new LayoutColumn(
								$this->tableCompetitionExtraPartNumber(), 6
							)
						);
				} else {
					$ExtraPartNumber = '';
				}

				//Wettbewerbsdaten
				$LayoutGroupCompetition =
					new LayoutGroup(
						array(
							$ExtraPartNumber,
							new LayoutRow(
								new LayoutColumn(
									$this->tableCompetitionDataPartNumber(), 12
								)
							)
						),
						new Title('Wettbewerbsdaten')
					);

			} else {
				$Table = new Warning('Die Teilenummer konnte nicht gefunden werden.');
			}
		}

		$Stage->setContent(
			new Layout(array(
				new LayoutGroup(
					new LayoutRow(
						new LayoutColumn(
							$this->fromSearchPartNumber()
						,4)
					)
				),
				$LayoutGroupDirectSearch,
				$LayoutGroupCompetition
			))
		);



//
//		$Stage->setContent(
//			new Layout(
//				new LayoutGroup(
//					new LayoutRow(
//						array(
//							new LayoutColumn(
//
//								new Form(
//									new FormGroup(
//										new FormRow(
//											array(
//												new FormColumn(
//													new Panel('Suche', array(
//														new TextField('PartNumber', 'Teilenummer', 'Teilenummer eingeben', new Search()),
//														new TextField('PartNumber', 'Teilenummer', 'Teilenummer eingeben', new Search())
//													)), 4
//												),
//												new FormColumn(
//													(new SelectBox(
//														'ProductManager', 'Produktmanager', array(
//														0 => '',
//														'AS' => 'Andreas Schneider',
//														'SK' => 'Stefan Klinke',
//														'SH' => 'Stefan Hahn'
//													)))->setDefaultValue('AS'), 4
//												),
//												new FormColumn(
//													new AutoCompleter('MarketingCode', 'Marketingcode', 'Marketingcode eingeben', array('1P123')), 4
//												)
//											)
//										)
//									)
//									, array(
//										new Primary('anzeigen', new Search()),
//										new Reset('zurücksetzen')
//									)
//								)
//							)
//						)
//					)
//				)
//			)
//		);

		return $Stage;
	}

	public function frontendSearchProductManager( $Search = null )
	{
		$Stage = new Stage('Direktsuche', 'Produktmanager');
		$this->buttonStageDirectSearch($Stage);

		$LayoutGroupDirectSearch = '';
		if( $Search ) {
			if (empty($Result)) {
				$LayoutGroupDirectSearch =
					new LayoutGroup(
						array(
							new LayoutRow(
								new LayoutColumn(
									$this->tableMasterDataProductManager()
									, 6
								)
							),
							new LayoutRow(
								new LayoutColumn(
									$this->tableSalesDataProductManager()
									, 12
								)
							),
							new LayoutRow(
								new LayoutColumn(
									'&nbsp;'
								)
							),
							new LayoutRow(
								new LayoutColumn(
									'Diagramme', 12
								)
							)
						),
						new Title('Direktsuche')
					);
			} else {
				$Table = new Warning('Der Produktmanager konnte nicht gefunden werden.');
			}
		}

		$Stage->setContent(
			new Layout(array(
				new LayoutGroup(
					new LayoutRow(
						new LayoutColumn(
							$this->fromSearchProductManager()
							,4
						)
					)
				),
				$LayoutGroupDirectSearch
			))
		);
		return $Stage;
	}

	public function frontendSearchMarketingCode( $Search = null )
	{
		$Stage = new Stage('Direktsuche', 'Marketingcode');
		$this->buttonStageDirectSearch($Stage);

		$LayoutGroupDirectSearch = '';
		if( $Search ) {
			if (empty($Result)) {
				$LayoutGroupDirectSearch =
					new LayoutGroup(
						array(
							new LayoutRow(
								new LayoutColumn(
									$this->tableMasterDataMarketingCode()
									, 6
								)
							),
							new LayoutRow(
								new LayoutColumn(
									$this->tableSalesDataMarketingCode()
									, 12
								)
							),
							new LayoutRow(
								new LayoutColumn(
									'&nbsp;'
								)
							),
							new LayoutRow(
								new LayoutColumn(
									'Diagramme', 12
								)
							)
						),
						new Title('Direktsuche')
					);
			} else {
				$Table = new Warning('Der Marketingcode konnte nicht gefunden werden!');
			}
		}

		$Stage->setContent(
			new Layout(array(
				new LayoutGroup(
					new LayoutRow(
						new LayoutColumn(
							$this->fromSearchMarketingCode()
						,4)
					)
				),
				$LayoutGroupDirectSearch
			))
		);

		return $Stage;
	}

//	public function frontendDirectSearch($PartNumber = null, $ProductManager = null, $MarketingCode = null)
//	{
//		//$this->getDebugger()->screenDump($PartNumber);
//		$Stage = new Stage('Direktsuche');
//		$Stage->setMessage('');
//
//
//		$Text = array();
//		if (!empty($PartNumber)) {
//			$Text = array(
//				new LayoutRow(
//					array(
//						new LayoutColumn(
//							'Stammdaten', 6
//						),
//						new LayoutColumn(
//							'Preisdaten', 6
//						)
//					)
//				)
//			);
//		}
//
//		//Debugger::screenDump();
//
//		$Stage->setContent(
//			$this->FormSearch($PartNumber, $ProductManager, $MarketingCode)
//		);
////		$Stage->setContent(new Layout(
////							array(
////
////								new LayoutGroup(
////									$Text
////								)
////							)
////						));
//		$Stage1 = new Stage('Test');
//		$Stage1->setContent(new Layout(
//			new LayoutGroup(
//				new LayoutRow(
//					array(
//						new LayoutColumn(
//							(
//							new Form(
//								new FormGroup(
//									new FormRow(
//										array(
//											new FormColumn(
//												new TextField('PartNumber', 'Teilenummer', 'Teilenummer eingeben', new Search()), 4
//											),
//											new FormColumn(
//												new SelectBox('ProductManager', 'Produktmanager', array('0' => '-[ Nicht ausgewählt ]-', 'AS' => 'Andreas Schneider', 'SK' => 'Stefan Klinke', 'SH' => 'Stefan Hahn')), 4
//											),
//											new FormColumn(
//												new AutoCompleter('MarketingCode', 'Marketingcode', 'Marketingcode eingeben', array('1P123')), 4
//											)
//										)
//									)
//								)
//							)
//							)->appendFormButton(new Primary('anzeigen', new Search()))
//						)
//					)
//				)
//			)
//		));
//		return $Stage . $Stage1;
//	}

//	private function FormSearch($PartNumber = null, $ProductManager = null, $MarketingCode = null)
//	{
//		$this->getDebugger()->screenDump($PartNumber, $ProductManager);
//		return new Layout(
//			new LayoutGroup(
//				new LayoutRow(
//					array(
//						new LayoutColumn(
//
//							new Form(
//								new FormGroup(
//									new FormRow(
//										array(
//											new FormColumn(
//												new TextField('PartNumber', 'Teilenummer', 'Teilenummer eingeben', new Search()), 4
//											),
//											new FormColumn(
//												new SelectBox('ProductManager', 'Produktmanager', array('0' => '-[ Nicht ausgewählt ]-', 'AS' => 'Andreas Schneider', 'SK' => 'Stefan Klinke', 'SH' => 'Stefan Hahn')), 4
//											),
//											new FormColumn(
//												new AutoCompleter('MarketingCode', 'Marketingcode', 'Marketingcode eingeben', array('1P123')), 4
//											)
//										)
//									)
//								)
//								, new Primary('anzeigen', new Search()))
//
//						)
//					)
//				)
//			)
//		);
//,
//						new LayoutColumn(
//							(
//								new Form(
//									new FormGroup(
//										new FormRow(
//											new FormColumn(
//												new SelectBox('ProductManager', 'Produktmanager', array( '0' => '-[ Nicht ausgewählt ]-', 'AS' => 'Andreas Schneider', 'SK' => 'Stefan Klinke', 'SH' => 'Stefan Hahn' )),
//												12
//											)
//										)
//									), $ProductManager
//								)
//							)->appendFormButton(new Primary('anzeigen', new Search()))
//							, 4
//						),
//						new LayoutColumn(
//							(
//								new Form(
//									new FormGroup(
//										new FormRow(
//											new FormColumn(
//												new AutoCompleter('MarketingCode', 'Marketingcode', 'Marketingcode eingeben', array('1P123')),
//												12
//											)
//										)
//									), $MarketingCode
//								)
//							)->appendFormButton(new Primary('anzeigen', new Search()))
//							, 4
//						)
//					)
//				)
//			)
//		);
//	}
//
	/**
	 * @return Form
	 */
	private function fromSearchPartNumber()
	{
		return new Form(
			new FormGroup(
				new FormRow(
					array(
						new FormColumn(
							new Panel('Suche', array(
								(new TextField('Search[PartNumber]', 'Teilenummer', 'Teilenummer eingeben', new Search()))
								->setRequired()
							), Panel::PANEL_TYPE_INFO)
						),
					)
				)
			)
			, array(
				new Primary('anzeigen', new Search()),
				new Reset('zurücksetzen')
			)
		);
	}

	private function fromSearchProductManager()
	{
		/*array('{{Name}} {{Bereich}}' => $PMData)*/

		return new Form(
			new FormGroup(
				new FormRow(
					array(
						new FormColumn(
							new Panel('Suche', array(
								(new SelectBox('Search[ProductManager]', 'Produktmanager',  array(0 => '-[ Nicht ausgewählt ]-', 'AS' => 'Andreas Schneider', 'SK' => 'Stefan Klinke', 'SH' => 'Stefan Hahn') ))
								->setRequired()
							), Panel::PANEL_TYPE_INFO)
						),
					)
				)
			)
			, array(
				new Primary('anzeigen', new Search()),
				new Reset('zurücksetzen')
			)
		);
	}

	private function fromSearchMarketingCode()
	{
		return new Form(
			new FormGroup(
				new FormRow(
					array(
						new FormColumn(
							new Panel('Suche', array(
								(new AutoCompleter('Search[MarketingCode]', 'Marketingcode', 'Marketingcode eingeben', array('1P123')))
								->setRequired()
							), Panel::PANEL_TYPE_INFO)
						),
					)
				)
			)
			, array(
				new Primary('anzeigen', new Search()),
				new Reset('zurücksetzen')
			)
		);
	}

	private function tableMasterDataPartNumber() {
		return new TableData(
			array(
				array(
					'Description' => new Bold( 'Teilenummer' ),
					'Value' => 'A1234'
				),
				array(
					'Description' => 'Sortimentsgruppe',
					'Value' => '123'
				),
				array(
					'Description' => 'Marketingcode',
					'Value' => '1P23'
				),
				array(
					'Description' => 'Warengruppe',
					'Value' => '1P23'
				),
				array(
					'Description' => 'Sparte',
					'Value' => 'Pkw'
				),
				array(
					'Description' => 'Produktmanager',
					'Value' => 'Andreas Schneider'
				),
				array(
					'Description' => 'Hauptlieferant',
					'Value' => 'unbekannt'
				),
			),
			new TableTitle('Allgemeine Informationen'),
			array( 'Description' => 'Bezeichnung', 'Value' => '' ),
			array(
				"columnDefs" => array(
			        array('width' => '40%', 'targets' => '0' ),
					array('width' => '60%', 'targets' => '1' )
				),
				"paging"         => false, // Deaktivieren Blättern
			    "iDisplayLength" => -1,    // Alle Einträge zeigen
			    "searching"      => false, // Deaktivieren Suchen
			    "info"           => false,  // Deaktivieren Such-Info
				"sort"           => false   //Deaktivierung Sortierung der Spalten
			)
		);
	}

	private function tableMasterDataMarketingCode() {
		return new TableData(
			array(
				array(
					'Description' => 'Marketingcode',
					'Value' => '1P23'
				),
				array(
					'Description' => 'Warengruppe',
					'Value' => '1P23'
				),
				array(
					'Description' => 'P+M',
					'Value' => '5%'
				),
				array(
					'Description' => 'Anzahl TNR',
					'Value' => 'Andreas Schneider'
				),
				array(
					'Description' => 'Produktmanager',
					'Value' => 'Andreas Schneider (Bereich)'
				),
			),
			new TableTitle('Allgemeine Informationen'),
			array( 'Description' => 'Bezeichnung', 'Value' => '' ),
			array(
				"columnDefs" => array(
			        array('width' => '40%', 'targets' => '0' ),
					array('width' => '60%', 'targets' => '1' )
				),
				"paging"         => false, // Deaktivieren Blättern
			    "iDisplayLength" => -1,    // Alle Einträge zeigen
			    "searching"      => false, // Deaktivieren Suchen
			    "info"           => false,  // Deaktivieren Such-Info
				"sort"           => false   //Deaktivierung Sortierung der Spalten
			)
		);
	}

	private function tableMasterDataProductManager() {
		return new TableData(
			array(
				array(
					'Description' => 'Marketingcode',
					'Value' => '1P23'
				),
				array(
					'Description' => 'Warengruppe',
					'Value' => '1P23'
				),
				array(
					'Description' => 'P+M',
					'Value' => '5%'
				),
				array(
					'Description' => 'Anzahl TNR',
					'Value' => 'Andreas Schneider'
				),
				array(
					'Description' => 'Produktmanager',
					'Value' => 'Andreas Schneider (Bereich)'
				),
			),
			new TableTitle('Allgemeine Informationen'),
			array( 'Description' => 'Bezeichnung', 'Value' => '' ),
			array(
				"columnDefs" => array(
			        array('width' => '40%', 'targets' => '0' ),
					array('width' => '60%', 'targets' => '1' )
				),
				"paging"         => false, // Deaktivieren Blättern
			    "iDisplayLength" => -1,    // Alle Einträge zeigen
			    "searching"      => false, // Deaktivieren Suchen
			    "info"           => false,  // Deaktivieren Such-Info
				"sort"           => false   //Deaktivierung Sortierung der Spalten
			)
		);
	}

	private function tablePriceDataPartNumber() {
		$Rw = 0;
//		$CalcRules = $this->getCalculationRules();
//		$CalcRules->calcExpansionFactor()

		if( $Rw != 0 ) {
			$PriceDescription = 'BLP / VP<br/>BLP / TP<br/>NLP / VP<br/>NLP / TP';
			$PriceValue = '';
		}
		else {
			$PriceDescription = 'BLP / VP<br/>NLP / VP';
			$PriceValue = '';
		}

		return new TableData(
			array(
				array(
					'Description' => $PriceDescription,
					'Value' => $PriceValue
				),
				array(//PartsMoreProzent'
					'Description' => 'P+M '.number_format(0, 2, ',', '.').'%<br>NLP / P+M',
					'Value' => '123'
				),
				array(
					'Description' => 'Rabattgruppe',
					'Value' => '1P23'
				),
				array(
					'Description' => 'variable Kosten',
					'Value' => '1P23'
				),
				array(
					'Description' => 'Preis gültig ab<br/>TNR-Status',
					'Value' => 'Pkw'
				),
				array(
					'Description' => 'Konzern-DB',
					'Value' => 'Andreas Schneider'
				),
				array(
					'Description' => 'FC-Grenze',
					'Value' => 'unbekannt'
				),
			),
			new TableTitle('Preis- und Kosteninformationen'),
			array( 'Description' => 'Bezeichnung ', 'Value' => '' ),
			array(
				"columnDefs" => array(
			        array('width' => '40%', 'targets' => '0' ),
					array('width' => '60%', 'targets' => '1' )
				),
				"paging"         => false, // Deaktivieren Blättern
			    "iDisplayLength" => -1,    // Alle Einträge zeigen
			    "searching"      => false, // Deaktivieren Suchen
			    "info"           => false,  // Deaktivieren Such-Info
				"sort"           => false   //Deaktivierung Sortierung der Spalten
			)
		);
	}

	private function tablePriceDevelopmentPartNumber() {
		return new TableData(
			array(
				array(
					'Year' => '',
					'GrossPrice' => 'A1234',
					'NetPrice' => 'A1234',
					'Discount' => 'A1234',
					'Rw' => 'A1234',
					'VariableCosts' => 'A1234',
					'CoverageContribution' => 'A1234'
				),
				array(
					'Year' => '',
					'GrossPrice' => 'A1234',
					'NetPrice' => 'A1234',
					'Discount' => 'A1234',
					'Rw' => 'A1234',
					'VariableCosts' => 'A1234',
					'CoverageContribution' => 'A1234'
				),
				array(
					'Year' => '',
					'GrossPrice' => 'A1234',
					'NetPrice' => 'A1234',
					'Discount' => 'A1234',
					'Rw' => 'A1234',
					'VariableCosts' => 'A1234',
					'CoverageContribution' => 'A1234'
				),
				array(
					'Year' => '',
					'GrossPrice' => 'A1234',
					'NetPrice' => 'A1234',
					'Discount' => 'A1234',
					'Rw' => 'A1234',
					'VariableCosts' => 'A1234',
					'CoverageContribution' => 'A1234'
				),
				array(
					'Year' => '',
					'GrossPrice' => 'A1234',
					'NetPrice' => 'A1234',
					'Discount' => 'A1234',
					'Rw' => 'A1234',
					'VariableCosts' => 'A1234',
					'CoverageContribution' => 'A1234'
				),
				array(
					'Year' => '',
					'GrossPrice' => 'A1234',
					'NetPrice' => 'A1234',
					'Discount' => 'A1234',
					'Rw' => 'A1234',
					'VariableCosts' => 'A1234',
					'CoverageContribution' => 'A1234'
				),
				array(
					'Year' => '',
					'GrossPrice' => 'A1234',
					'NetPrice' => 'A1234',
					'Discount' => 'A1234',
					'Rw' => 'A1234',
					'VariableCosts' => 'A1234',
					'CoverageContribution' => 'A1234'
				),
			),
			new TableTitle('Preis- und Kostenentwicklung'),
			array(
				'Year' => 'Jahr',
				'GrossPrice' => 'BLP',
				'NetPrice' => 'NLP',
				'Discount' => 'RG',
				'Rw' => 'RW',
				'VariableCosts' => 'VK',
				'CoverageContribution' => 'DB'
			),
			array(
				"columnDefs" => array(
			        array('width' => '40%', 'targets' => '0' ),
					array('width' => '60%', 'targets' => '1' )
				),
				"paging"         => false, // Deaktivieren Blättern
			    "iDisplayLength" => -1,    // Alle Einträge zeigen
			    "searching"      => false, // Deaktivieren Suchen
			    "info"           => false,  // Deaktivieren Such-Info
				"sort"           => false   //Deaktivierung Sortierung der Spalten
			)
		);
	}

	private function tableSalesDataPartNumber() {
		return new TableData(
			array(
				array(
					'Description' => new Bold( 'Teilenummer' ),
					'Value' => 'A1234'
				),
				array(
					'Description' => 'Sortimentsgruppe',
					'Value' => '123'
				),
				array(
					'Description' => 'Marketingcode',
					'Value' => '1P23'
				),
				array(
					'Description' => 'Warengruppe',
					'Value' => '1P23'
				),
				array(
					'Description' => 'Sparte',
					'Value' => 'Pkw'
				),
				array(
					'Description' => 'Produktmanager',
					'Value' => 'Andreas Schneider'
				),
				array(
					'Description' => 'Hauptlieferant',
					'Value' => 'unbekannt'
				),
			),
			new TableTitle('Controlling-Informationen'),
			array( 'Description' => 'Bezeichnung', 'Value' => '', 'Value2' => ' ' ),
			array(
				"columnDefs" => array(
			        array('width' => '40%', 'targets' => '0' ),
					array('width' => '60%', 'targets' => '1' )
				),
				"paging"         => false, // Deaktivieren Blättern
			    "iDisplayLength" => -1,    // Alle Einträge zeigen
			    "searching"      => false, // Deaktivieren Suchen
			    "info"           => false,  // Deaktivieren Such-Info
				"sort"           => false   //Deaktivierung Sortierung der Spalten
			)
		);
	}

	private function tableSalesDataMarketingCode() {
		return new TableData(
			array(
				array(
					'Description' => new Bold( 'Teilenummer' ),
					'Value' => 'A1234'
				),
				array(
					'Description' => 'Sortimentsgruppe',
					'Value' => '123'
				),
				array(
					'Description' => 'Marketingcode',
					'Value' => '1P23'
				),
				array(
					'Description' => 'Warengruppe',
					'Value' => '1P23'
				),
				array(
					'Description' => 'Sparte',
					'Value' => 'Pkw'
				),
				array(
					'Description' => 'Produktmanager',
					'Value' => 'Andreas Schneider'
				),
				array(
					'Description' => 'Hauptlieferant',
					'Value' => 'unbekannt'
				),
			),
			new TableTitle('Controlling-Informationen'),
			array( 'Description' => 'Bezeichnung', 'Value' => '', 'Value2' => ' ' ),
			array(
				"columnDefs" => array(
			        array('width' => '40%', 'targets' => '0' ),
					array('width' => '60%', 'targets' => '1' )
				),
				"paging"         => false, // Deaktivieren Blättern
			    "iDisplayLength" => -1,    // Alle Einträge zeigen
			    "searching"      => false, // Deaktivieren Suchen
			    "info"           => false,  // Deaktivieren Such-Info
				"sort"           => false   //Deaktivierung Sortierung der Spalten
			)
		);
	}

	private function tableSalesDataProductManager() {
		return new TableData(
			array(
				array(
					'Description' => new Bold( 'Teilenummer' ),
					'Value' => 'A1234'
				),
				array(
					'Description' => 'Sortimentsgruppe',
					'Value' => '123'
				),
				array(
					'Description' => 'Marketingcode',
					'Value' => '1P23'
				),
				array(
					'Description' => 'Warengruppe',
					'Value' => '1P23'
				),
				array(
					'Description' => 'Sparte',
					'Value' => 'Pkw'
				),
				array(
					'Description' => 'Produktmanager',
					'Value' => 'Andreas Schneider'
				),
				array(
					'Description' => 'Hauptlieferant',
					'Value' => 'unbekannt'
				),
			),
			new TableTitle('Controlling-Informationen'),
			array( 'Description' => 'Bezeichnung', 'Value' => '', 'Value2' => ' ' ),
			array(
				"columnDefs" => array(
			        array('width' => '40%', 'targets' => '0' ),
					array('width' => '60%', 'targets' => '1' )
				),
				"paging"         => false, // Deaktivieren Blättern
			    "iDisplayLength" => -1,    // Alle Einträge zeigen
			    "searching"      => false, // Deaktivieren Suchen
			    "info"           => false,  // Deaktivieren Such-Info
				"sort"           => false   //Deaktivierung Sortierung der Spalten
			)
		);
	}

	private function tableCompetitionExtraPartNumber() {
		return new TableData(
			array(
				array(
					'Description' => new Bold( 'Dimension' ),
					'Value' => 'A1234'
				),
				array(
					'Description' => 'Profil',
					'Value' => '123'
				),
				array(
					'Description' => 'Rad',
					'Value' => '1P23'
				),
				array(
					'Description' => 'Warengruppe',
					'Value' => '1P23'
				),
				array(
					'Description' => 'Sparte',
					'Value' => 'Pkw'
				),
				array(
					'Description' => 'Produktmanager',
					'Value' => 'Andreas Schneider'
				),
				array(
					'Description' => 'Hauptlieferant',
					'Value' => 'unbekannt'
				),
			),
			new TableTitle('Zusätzliche Informationen'),
			array( 'Description ' => 'Bezeichnung', 'Value' => '' ),
			array(
				"columnDefs" => array(
			        array('width' => '40%', 'targets' => '0' ),
					array('width' => '60%', 'targets' => '1' )
				),
				"paging"         => false, // Deaktivieren Blättern
			    "iDisplayLength" => -1,    // Alle Einträge zeigen
			    "searching"      => false, // Deaktivieren Suchen
			    "info"           => false,  // Deaktivieren Such-Info
				"sort"           => false   //Deaktivierung Sortierung der Spalten
			)
		);
	}

	private function tableCompetitionDataPartNumber() {
		return new TableData(
			array(
				array(
					'Description' => 'Wettbewerber',
					'Manufacturer' => 'Hersteller',
					'PeriodOfTime' => 'Zeitraum',
					'NetPrice' => 'NLP',
					'GrossPrice' => 'BLP',
					'Discount' => 'Rabatt',
					'WV' => 'WV',
					'EA' => 'EA',
					'VP' => 'VP',
					'Comments' => 'Kommentar',
					'RetailNumber' => 'VF',
					'DeleteButton' => 'Löschen',
				),
				array(
					'Description' => 'Wettbewerber',
					'Manufacturer' => 'Hersteller',
					'PeriodOfTime' => 'Zeitraum',
					'NetPrice' => 'NLP',
					'GrossPrice' => 'BLP',
					'Discount' => 'Rabatt',
					'WV' => 'WV',
					'EA' => 'EA',
					'VP' => 'VP',
					'Comments' => 'Kommentar',
					'RetailNumber' => 'VF',
					'DeleteButton' => 'Löschen',
				),
				array(
					'Description' => 'Wettbewerber',
					'Manufacturer' => 'Hersteller',
					'PeriodOfTime' => 'Zeitraum',
					'NetPrice' => 'NLP',
					'GrossPrice' => 'BLP',
					'Discount' => 'Rabatt',
					'WV' => 'WV',
					'EA' => 'EA',
					'VP' => 'VP',
					'Comments' => 'Kommentar',
					'RetailNumber' => 'VF',
					'DeleteButton' => 'Löschen',
				),
			),
			new TableTitle('Wettbewerbsdaten'),
			array(
				'Description' => 'Wettbewerber',
				'Manufacturer' => 'Hersteller',
				'PeriodOfTime' => 'Zeitraum',
				'NetPrice' => 'NLP',
				'GrossPrice' => 'BLP',
				'Discount' => 'Rabatt',
				'WV' => 'WV',
				'EA' => 'EA',
				'VP' => 'VP',
				'Comments' => 'Kommentar',
				'RetailNumber' => 'VF',
				'DeleteButton' => 'Löschen',
			),
			array(
				"columnDefs" => array(
			        array('width' => '40%', 'targets' => '0' ),
					array('width' => '60%', 'targets' => '1' )
				),
				"paging"         => false, // Deaktivieren Blättern
			    "iDisplayLength" => -1,    // Alle Einträge zeigen
			    "searching"      => false, // Deaktivieren Suchen
			    "info"           => false,  // Deaktivieren Such-Info
				"sort"           => false   //Deaktivierung Sortierung der Spalten
			)
		);
	}
}
