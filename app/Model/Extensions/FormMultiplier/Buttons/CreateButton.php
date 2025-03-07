<?php declare(strict_types = 1);

namespace App\Model\Extensions\FormMultiplier\Buttons;

use App\Model\Extensions\FormMultiplier\Multiplier;
use App\Model\Extensions\FormMultiplier\Submitter;
use Nette\SmartObject;

final class CreateButton
{

	use SmartObject;

	/** @var callable[] */
	public array $onCreate = [];

	private ?string $caption = null;

	private int $copyCount;

	/** @var mixed[]|null */
	private ?array $validationScope = null;

	/** @var string[] */
	private array $classes = [];

	public function __construct(?string $caption, int $copyCount = 1)
	{
		$this->caption = $caption;
		$this->copyCount = $copyCount;
	}

	public function addOnCreateCallback(callable $onCreate): self
	{
		$this->onCreate[] = $onCreate;

		return $this;
	}

	public function setNoValidate(): self
	{
		$this->setValidationScope([]);

		return $this;
	}

	public function addClass(string $class): self
	{
		$this->classes[] = $class;

		return $this;
	}

	/**
	 * @param mixed[]|null $validationScope
	 */
	public function setValidationScope(?array $validationScope): self
	{
		$this->validationScope = $validationScope;

		return $this;
	}

	public function getComponentName(): string
	{
		return Multiplier::SUBMIT_CREATE_NAME . ($this->copyCount === 1 ? '' : $this->copyCount);
	}

	public function getCopyCount(): int
	{
		return $this->copyCount;
	}

	public function create(Multiplier $multiplier): Submitter
	{
		$button = new Submitter($this->caption, $this->copyCount);

		$button->setHtmlAttribute('class', implode(' ', $this->classes));
		$button->setValidationScope($this->validationScope ?? [$multiplier])
			->setOmitted();

		foreach ($this->onCreate as $callback) {
			$callback($button);
		}

		$button->onClick[] = [$multiplier, 'resetFormEvents'];

		return $button;
	}

}
