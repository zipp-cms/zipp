<?php
/*
@package: Magma PHP Template Engine
@author: Sören Meier <info@s-me.ch>
@version: 0.1.2 <2019-08-23>
*/

namespace MagmaTemplate;

class BlockParser {

	protected $strDivider = null;

	protected $functions = [];

	protected $tree = [];

	public function __construct( array $functions ) {

		$this->strDivider = new CharDivider( '\'' );
		$this->functions = $functions;

	}

	public function addFn( string $fn, string $execution ) {
		$this->functions[$fn] = $execution;
	}

	public function parse( string $block ) {

		$block = trim( $block );

		$type = $this->checkType( $block );

		switch ( $this->checkType( $block ) ) {
			case 0:
				return $this->parseStatement( $block );
			case 1:
				return $this->parseEndStatement( $block );
			case 2:
				return $this->parseAssignments( $block );
			case 3:
				return $this->parseExpression( $block );
		}

	}

	protected function checkType( string $block ) {

		$matches = [
			'/^(if|for|block|elif)\s/',
			'/^(else|end)$/',
			'/^[A-Za-z0-9.\[\]]*\s*(=|[+\/\-*.]=)(?=\s)/'
		];

		foreach ( $matches as $k => $match )
			if ( preg_match( $match, $block ) )
				return $k;

		return 3;

	}

	protected function parseStatement( string $block ) {

		// (if|for|block|elif)

		$p = strpos( $block, ' ' );
		$type = substr( $block, 0, $p );
		$exp = trim( substr( $block, $p ) );

		$o = '';

		switch ( $type ) {

			case 'if':
				$exp = $this->baseReplace( $exp );
				$this->tree = [ $type, $exp, $this->tree ];

				$o = sprintf( 'if ( %s ) {', $exp );
				break;

			case 'elif':
				$exp = $this->baseReplace( $exp );

				if ( $this->tree[0] !== 'if' )
					throw new \Error( sprintf( 'elif (exp: %s) can only follow an if statement', $exp ) );

				$this->tree = [ 'if', $exp, $this->tree[2] ];

				$o = sprintf( '} elseif ( %s ) {', $exp );
				break;

			case 'for':
				$parts = array_map( 'trim', explode( ' in ', $exp ) );

				if ( count( $parts ) !== 2 )
					throw new \Error( sprintf( 'for loop (exp: %s) needs to have an in', $exp ) );

				$assign = str_replace( ',', ' =>', $this->replaceVariables( $parts[0] ) );
				$item = $this->replaceVariables( $parts[1] );
				$item = $this->replaceCoreFunctions( $item );

				$this->tree = [ $type, [$assign, $item], $this->tree ];

				$o = sprintf( 'foreach ( %s as %s ) {', $item, $assign );
				break;

			case 'block':
				$name = $this->baseReplace( $exp );
				$this->tree = [ $type, $name, $this->tree ];
				$o = 'startBlock();';
				$o = $this->replaceCoreFunctions( $o );
				break;

		}


		return sprintf( '<?php %s /* statement %s */ ?>', $o, $type );

	}

	protected function parseEndStatement( string $block ) {

		$tree = $this->tree;
		$type = $block;

		if ( count( $tree ) !== 3 )
			throw new Error( 'cannot end a statement when no statement is opened' );

		$o = '';

		switch ( $tree[0] ) {

			case 'if':

				$o = '}';
				if ( $type === 'else' )
					$o .= ' else {';
				else {
					$this->tree = $tree[2];
				}

				break;

			case 'block':
				$o = sprintf( 'endBlock( %s )', $tree[1] );
				$o = $this->replaceCoreFunctions( $o );
				$this->tree = $tree[2];
				break;

			case 'for':

				if ( $type === 'else' ) {
					$exp = sprintf( 'count( %s ) === 0', $tree[1][1] );
					$o = sprintf( '} if ( %s ) {', $exp );
					$this->tree = [ 'if', $exp, $tree[2] ];
				} else {
					$o .= '}';
					$this->tree = $tree[2];
				}

				break;

		}

		return sprintf( '<?php %s ?>', $o );

	}

	protected function parseAssignments( string $block ) {

		$o = $this->baseReplace( $block ); // maybe should check for assigning functions like (esc, inc, rep)
		return sprintf( '<?php %s; /* assignment */ ?>', $o );

	}

	protected function parseExpression( string $block ) {

		$block = $this->replaceFunctions( $block );

		$o = $this->baseReplace( $block );

		return sprintf( '<?= %s /* expression */ ?>', $o );

	}

	protected function baseReplace( string $block ) {

		$o = '';
		foreach ( $this->strDivider->divide( $block, true ) as $p ) {

			if ( $p[0] )
				$o .= sprintf( '\'%s\'', $p[1] );
			else {
				$v = $this->replaceLogical( $p[1] );
				$v = $this->replaceVariables( $v );
				$v = $this->replaceTernary( $v );
				$v = $this->replaceCoreFunctions( $v );
				$o .= $v;
			}

		}

		return $o;

	}

	protected function replaceFunctions( string $block ) {

		$functions = $this->functions; // will need to Change this

		return preg_replace( sprintf( '/^(%s)\s+(.*)$/', implode( '|', array_keys( $functions ) ) ), '$1( $2 )', $block );

	}

	protected function replaceCoreFunctions( string $block ) {

		$functions = $this->functions; // will need to Change this

		$search = [];
		$res = [];
		foreach ( $functions as $k => $fn ) {
			$search[] = sprintf( '/(?<=\s|^)%s(?=\()/', $k );
			$res[] = $fn;
		}

		return preg_replace( $search, $res, $block );

	}

	protected function replaceVariables( string $s ) {

		$s = preg_replace( '/(\d+)\.\.(\d+)/', 'between( $1, $2 )', $s );
		$s = preg_replace( '/(\d+)\.\.=(\d+)/', 'between( $1, $2 + 1 )', $s );
		$s = preg_replace( '/(?<=^|\s|!|>|=)([a-zA-Z]\w*)(?=\s|\.|,|\[|$|\])/', '\$$1', $s );
		$s = preg_replace( '/(?<=\w|\]|\))(\.)(?=[a-zA-Z])/', '->', $s );
		return $s;

	}

	protected function replaceTernary( string $s ) {
		$s = preg_replace( '/:$/', ': \'\'', $s );
		$s = preg_replace( '/\?\?$/', '?? \'\'', $s );
		return $s;
	}

	protected function replaceLogical( string $s ) {
		$s = preg_replace( '/(?<=\s)and(?=\s)/', '&&', $s );
		$s = preg_replace( '/(?<=\s)or(?=\s)/', '||', $s );
		$s = preg_replace( '/(?<=\s)([!=]=)(?=\s)/', '$1=', $s );
		return $s;
	}

}