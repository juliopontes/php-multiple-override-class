<?php
abstract class JOverride
{
	private static $map;
	private static $overrides;
	private static $classMap;

	/**
	 * Know if base class is already loaded
	 * 
	 * @var boolean
	 */
	private static $loadedDefault = false;

	static public function map($class_name, $file)
	{
		self::$map[$class_name] = $file;
	}

	static public function override($class_name, $file)
	{
		settype(self::$overrides[$class_name], 'array');
		array_push(self::$overrides[$class_name], $file);
	}

	static public function getOverrides()
	{
		return self::$overrides;
	}

	static final public function run()
	{	
		foreach (self::$overrides as $class => $filename)
		{
			settype($filename,'array');
			foreach ($filename as $key => $file)
			{
				$overrideFile = new JOverrideFile($file);
				$sourceClass = $overrideFile->getOriginalClass();
				$extendedClass = $overrideFile->getExtendedClass();
				
				if ($key == count($filename) -1)
				{
					if ($extendedClass == $class)
					{
						//load default class
						if (!self::$loadedDefault)
						{
							//load default class
							if (empty(self::$classMap[$class]))
							{
								self::$classMap[$class] = $sourceClass;
								$replaceClass = $class.'Default';
								
								if (!class_exists($class, false))
								{
									$source_filename = self::$map[$class];
									if (is_file($source_filename))
									{
										//load class and replace name by default
										$overrideSourceFile = new JOverrideFile($source_filename);
										$bufferContent = preg_replace('/'.$overrideSourceFile->getOriginalClass().'/',$replaceClass,$overrideSourceFile->getBuffer());
										$overrideSourceFile->setBuffer($bufferContent);
										
										$overrideSourceFile->load();
									}
									else 
									{
										throw new Exception(sprintf('Cant load %s class',$class), '95');
									}
								}
							}
						}
						else 
						{
							$replaceClass = self::$classMap[$class];
						}
					}
					
					$bufferContent = preg_replace('/extends '.$class.'/','extends '.$replaceClass,$overrideFile->getBuffer());
					$bufferContent = preg_replace('/class '.$sourceClass.'/','class '.$class,$bufferContent);
					$overrideFile->setBuffer($bufferContent);
				}
				else 
				{
					if ($extendedClass == $class)
					{
						if (!self::$loadedDefault)
						{
							//load default class
							if (empty(self::$classMap[$class]))
							{
								self::$classMap[$class] = $sourceClass;
								$replaceClass = $class.'Default';
								
								if (!class_exists($class, false))
								{
									$source_filename = self::$map[$class];
									if (is_file($source_filename))
									{
										//load class and replace name by default
										$overrideSourceFile = new JOverrideFile($source_filename);
										$bufferContent = preg_replace('/'.$overrideSourceFile->getOriginalClass().'/',$replaceClass,$overrideSourceFile->getBuffer());
										$overrideSourceFile->setBuffer($bufferContent);
										
										$overrideSourceFile->load();
									}
									else 
									{
										throw new Exception(sprintf('Cant load %s class',$class), '95');
									}
								}
							}
							self::$loadedDefault = true;
						}
						else 
						{
							$replaceClass = self::$classMap[$class];
						}
						self::$classMap[$class] = $sourceClass;
						$bufferContent = preg_replace('/extends '.$class.'/','extends '.$replaceClass,$overrideFile->getBuffer());
						
						$overrideFile->setBuffer($bufferContent);
						
						
					}
				}
				
				$overrideFile->load();
			}
		}
	}
}